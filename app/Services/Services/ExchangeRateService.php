<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected $apiKey;
    protected $baseUrl;
    protected $cacheDuration = 3600; // 1 hour

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.api_key');
        $this->baseUrl = config('services.exchangerate.base_url', 'https://api.exchangerate-api.com/v4/latest/');
    }

    /**
     * Refresh exchange rates for given currencies and store in database
     */
    public function refreshRates(string $baseCurrency, array $quoteCurrencies): int
    {
        try {
            $rates = $this->fetchRatesFromAPI($baseCurrency, $quoteCurrencies);

            if (empty($rates)) {
                Log::warning('No exchange rates fetched from API');
                return 0;
            }

            // Store rates in database
            $this->storeRates($baseCurrency, $rates);

            // Clear cache
            Cache::forget("exchange_rates_{$baseCurrency}");

            Log::info("Refreshed exchange rates for base currency {$baseCurrency}", [
                'quotes_count' => count($rates)
            ]);

            return count($rates);

        } catch (\Exception $e) {
            Log::error('Failed to refresh exchange rates', [
                'error' => $e->getMessage(),
                'base' => $baseCurrency,
                'quotes' => $quoteCurrencies
            ]);

            return 0;
        }
    }

    /**
     * Fetch rates from external API with multiple providers support
     */
    public function fetchRates(string $base, array $quotes, ?string $provider = null): array
    {
        $providers = $this->getProviders();

        if ($provider && isset($providers[$provider])) {
            return $this->fetchFromProvider($base, $quotes, $providers[$provider]);
        }

        // Try providers in order of preference
        foreach ($providers as $providerName => $config) {
            try {
                $rates = $this->fetchFromProvider($base, $quotes, $config);
                if (!empty($rates)) {
                    Log::info("Successfully fetched rates from provider: {$providerName}");
                    return $rates;
                }
            } catch (\Exception $e) {
                Log::warning("Provider {$providerName} failed: " . $e->getMessage());
                continue;
            }
        }

        // All providers failed, use fallback
        Log::warning('All providers failed, using fallback rates');
        return $this->getFallbackRates($base, $quotes);
    }

    /**
     * Get available providers configuration
     */
    protected function getProviders(): array
    {
        return [
            'exchangerate_api' => [
                'url' => 'https://api.exchangerate-api.com/v4/latest/',
                'key_required' => false,
            ],
            'currencyapi' => [
                'url' => 'https://api.currencyapi.com/v3/latest',
                'key_required' => true,
                'key_param' => 'apikey',
            ],
            'fixer' => [
                'url' => 'http://data.fixer.io/api/latest',
                'key_required' => true,
                'key_param' => 'access_key',
            ],
        ];
    }

    /**
     * Fetch rates from a specific provider
     */
    protected function fetchFromProvider(string $base, array $quotes, array $config): array
    {
        $url = $config['url'] . $base;

        $params = [];
        if ($config['key_required'] && $this->apiKey) {
            $keyParam = $config['key_param'] ?? 'api_key';
            $params[$keyParam] = $this->apiKey;
        }

        $response = Http::timeout(10)->get($url, $params);

        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \Exception('API error: ' . ($data['error']['info'] ?? 'Unknown error'));
        }

        $apiRates = $data['rates'] ?? [];

        // Filter only requested currencies
        $filteredRates = [];
        foreach ($quotes as $quote) {
            if (isset($apiRates[$quote])) {
                $filteredRates[$quote] = $apiRates[$quote];
            }
        }

        return $filteredRates;
    }

    /**
     * Fetch rates from external API (legacy method for backward compatibility)
     */
    protected function fetchRatesFromAPI(string $base, array $quotes): array
    {
        return $this->fetchRates($base, $quotes);
    }

    /**
     * Get fallback exchange rates
     */
    protected function getFallbackRates(string $base, array $quotes): array
    {
        // Simple fallback rates (these would be outdated but better than nothing)
        $fallbackRates = [
            'USD' => 1.0,
            'EUR' => 0.85,
            'GBP' => 0.73,
            'JPY' => 110.0,
            'CAD' => 1.25,
            'AUD' => 1.35,
            'CHF' => 0.92,
            'CNY' => 6.45,
            'UGX' => 3700,
            'KES' => 160,
            'TZS' => 2300,
            'RWF' => 1200,
            'BIF' => 2800,
        ];

        $rates = [];
        foreach ($quotes as $quote) {
            if (isset($fallbackRates[$quote])) {
                $rates[$quote] = $fallbackRates[$quote];
            }
        }

        return $rates;
    }

    /**
     * Store rates in database
     */
    protected function storeRates(string $base, array $rates, string $provider = 'default'): void
    {
        foreach ($rates as $quote => $rate) {
            ExchangeRate::updateOrCreate(
                [
                    'base' => $base,
                    'quote' => $quote,
                ],
                [
                    'rate' => $rate,
                    'provider' => $provider,
                    'fetched_at' => now(),
                ]
            );
        }
    }

    /**
     * Get exchange rates from database with caching
     */
    public function getRates(string $base): array
    {
        return Cache::remember(
            "exchange_rates_{$base}",
            $this->cacheDuration,
            function () use ($base) {
                return ExchangeRate::forBase($base)
                                  ->recent(120) // Within last 2 hours
                                  ->pluck('rate', 'quote')
                                  ->toArray();
            }
        );
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        // Try direct conversion
        $rate = ExchangeRate::getRate($from, $to);
        if ($rate !== null) {
            return $amount * $rate;
        }

        // Try cached rates
        $rates = $this->getRates($from);
        if (isset($rates[$to])) {
            return $amount * $rates[$to];
        }

        // Try reverse lookup
        $reverseRates = $this->getRates($to);
        if (isset($reverseRates[$from]) && $reverseRates[$from] > 0) {
            return $amount / $reverseRates[$from];
        }

        throw new \Exception("Exchange rate not available for {$from} to {$to}");
    }

    /**
     * Get exchange rate between two currencies
     */
    public function getRate(string $from, string $to): ?float
    {
        return ExchangeRate::getRate($from, $to);
    }

    /**
     * Check if rates need refresh
     */
    public function needsRefresh(string $base, int $maxAgeMinutes = 60): bool
    {
        $recentRate = ExchangeRate::forBase($base)
                                 ->recent($maxAgeMinutes)
                                 ->first();

        return $recentRate === null;
    }

    /**
     * Get supported currencies from database
     */
    public function getSupportedCurrencies(): array
    {
        return Currency::enabled()
                      ->pluck('code')
                      ->toArray();
    }
}