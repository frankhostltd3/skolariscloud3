<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * Base currency for all exchange rates (USD).
     */
    private const BASE_CURRENCY = 'USD';

    /**
     * API endpoints for exchange rate providers.
     */
    private const PROVIDERS = [
        'exchangerate-api' => 'https://api.exchangerate-api.com/v4/latest/',
        'fixer' => 'https://api.fixer.io/latest',
        'currencyapi' => 'https://api.currencyapi.com/v3/latest',
    ];

    /**
     * Fetch exchange rates from external API.
     *
     * @param string $baseCurrency Base currency code (default: USD)
     * @return array|null Array of currency codes with exchange rates, or null on failure
     */
    public function fetchRates(string $baseCurrency = self::BASE_CURRENCY): ?array
    {
        // Try to get from cache first (cache for 1 hour)
        $cacheKey = "exchange_rates_{$baseCurrency}";

        if ($rates = Cache::get($cacheKey)) {
            return $rates;
        }

        // Try each provider in order
        foreach (self::PROVIDERS as $provider => $endpoint) {
            try {
                $rates = $this->fetchFromProvider($provider, $endpoint, $baseCurrency);

                if ($rates) {
                    // Cache successful response for 1 hour
                    Cache::put($cacheKey, $rates, now()->addHour());

                    Log::info("Exchange rates fetched successfully from {$provider}", [
                        'base_currency' => $baseCurrency,
                        'rates_count' => count($rates),
                    ]);

                    return $rates;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch exchange rates from {$provider}", [
                    'error' => $e->getMessage(),
                ]);

                // Continue to next provider
                continue;
            }
        }

        Log::error('All exchange rate providers failed');

        return null;
    }

    /**
     * Fetch rates from a specific provider.
     *
     * @param string $provider Provider name
     * @param string $endpoint API endpoint
     * @param string $baseCurrency Base currency code
     * @return array|null
     */
    private function fetchFromProvider(string $provider, string $endpoint, string $baseCurrency): ?array
    {
        switch ($provider) {
            case 'exchangerate-api':
                return $this->fetchFromExchangeRateApi($endpoint, $baseCurrency);

            case 'fixer':
                return $this->fetchFromFixer($endpoint, $baseCurrency);

            case 'currencyapi':
                return $this->fetchFromCurrencyApi($endpoint, $baseCurrency);

            default:
                return null;
        }
    }

    /**
     * Fetch from exchangerate-api.com (free, no API key required).
     *
     * @param string $endpoint
     * @param string $baseCurrency
     * @return array|null
     */
    private function fetchFromExchangeRateApi(string $endpoint, string $baseCurrency): ?array
    {
        $response = Http::timeout(10)->get($endpoint . $baseCurrency);

        if ($response->successful() && isset($response['rates'])) {
            return $response['rates'];
        }

        return null;
    }

    /**
     * Fetch from fixer.io (requires API key in .env: FIXER_API_KEY).
     *
     * @param string $endpoint
     * @param string $baseCurrency
     * @return array|null
     */
    private function fetchFromFixer(string $endpoint, string $baseCurrency): ?array
    {
        $apiKey = config('services.fixer.api_key');

        if (!$apiKey) {
            return null; // Skip if no API key configured
        }

        $response = Http::timeout(10)->get($endpoint, [
            'access_key' => $apiKey,
            'base' => $baseCurrency,
        ]);

        if ($response->successful() && isset($response['rates'])) {
            return $response['rates'];
        }

        return null;
    }

    /**
     * Fetch from currencyapi.com (requires API key in .env: CURRENCY_API_KEY).
     *
     * @param string $endpoint
     * @param string $baseCurrency
     * @return array|null
     */
    private function fetchFromCurrencyApi(string $endpoint, string $baseCurrency): ?array
    {
        $apiKey = config('services.currencyapi.api_key');

        if (!$apiKey) {
            return null; // Skip if no API key configured
        }

        $response = Http::timeout(10)->get($endpoint, [
            'apikey' => $apiKey,
            'base_currency' => $baseCurrency,
        ]);

        if ($response->successful() && isset($response['data'])) {
            // CurrencyAPI returns data in different format, normalize it
            $rates = [];
            foreach ($response['data'] as $code => $data) {
                $rates[$code] = $data['value'] ?? null;
            }
            return $rates;
        }

        return null;
    }

    /**
     * Get a specific exchange rate for a currency pair.
     *
     * @param string $fromCurrency Source currency code
     * @param string $toCurrency Target currency code
     * @return float|null Exchange rate, or null if not available
     */
    public function getRate(string $fromCurrency, string $toCurrency): ?float
    {
        // If same currency, rate is 1.0
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        // Fetch rates from base currency (USD)
        $rates = $this->fetchRates(self::BASE_CURRENCY);

        if (!$rates) {
            return null;
        }

        // Get rates for both currencies
        $fromRate = $rates[$fromCurrency] ?? null;
        $toRate = $rates[$toCurrency] ?? null;

        if (!$fromRate || !$toRate) {
            return null;
        }

        // Calculate cross rate: (1 / fromRate) * toRate
        return (1 / $fromRate) * $toRate;
    }

    /**
     * Check if the service is available and working.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $rates = $this->fetchRates();
            return $rates !== null && count($rates) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
