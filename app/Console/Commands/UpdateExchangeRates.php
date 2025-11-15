<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\School;
use App\Services\ExchangeRateService;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:update-exchange-rates {--force : Update all currencies even if auto-update is disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update exchange rates for all tenant currencies from external API';

    /**
     * Exchange rate service instance.
     */
    private ExchangeRateService $exchangeRateService;

    /**
     * Tenant database manager instance.
     */
    private TenantDatabaseManager $manager;

    /**
     * Create a new command instance.
     */
    public function __construct(ExchangeRateService $exchangeRateService, TenantDatabaseManager $manager)
    {
        parent::__construct();
        $this->exchangeRateService = $exchangeRateService;
        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');

        // Check if the exchange rate service is available
        if (!$this->exchangeRateService->isAvailable()) {
            $this->error('Exchange rate service is not available. Please check your internet connection or API configuration.');
            return self::FAILURE;
        }

        // Fetch latest exchange rates
        $this->info('Fetching latest exchange rates from API...');
        $rates = $this->exchangeRateService->fetchRates();

        if (!$rates) {
            $this->error('Failed to fetch exchange rates from all providers.');
            return self::FAILURE;
        }

        $this->info('Exchange rates fetched successfully. Found ' . count($rates) . ' currencies.');

        // Update rates for each tenant
        $schools = School::query()->orderBy('id')->get();

        if ($schools->isEmpty()) {
            $this->info('No schools found.');
            return self::SUCCESS;
        }

        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($schools as $school) {
            $this->components->task("Updating exchange rates for {$school->name}", function () use ($school, $rates, $force, &$totalUpdated, &$totalSkipped) {
                $this->manager->runFor(
                    $school,
                    function () use ($rates, $force, &$totalUpdated, &$totalSkipped) {
                        $query = Currency::query()->where('code', '!=', 'USD'); // Don't update USD (base currency)

                        // If not forced, only update currencies with auto-update enabled
                        if (!$force) {
                            $query->where('auto_update_enabled', true);
                        }

                        $currencies = $query->get();

                        foreach ($currencies as $currency) {
                            if (isset($rates[$currency->code])) {
                                $newRate = $rates[$currency->code];

                                // Only update if rate has changed
                                if (abs($currency->exchange_rate - $newRate) > 0.000001) {
                                    $currency->exchange_rate = $newRate;
                                    $currency->last_updated_at = now();
                                    $currency->save();

                                    $totalUpdated++;
                                } else {
                                    $totalSkipped++;
                                }
                            } else {
                                // Currency code not found in API response
                                $totalSkipped++;
                            }
                        }
                    },
                    runMigrations: false
                );
            });
        }

        $this->newLine();
        $this->info("Exchange rates updated successfully!");
        $this->info("Updated: {$totalUpdated} currencies");
        $this->info("Skipped: {$totalSkipped} currencies (unchanged or not found)");

        return self::SUCCESS;
    }
}
