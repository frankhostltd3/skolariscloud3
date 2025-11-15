<?php

if (!function_exists('setting')) {
    /**
     * Get or set application settings.
     *
     * @param  string|array|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting(string|array|null $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return app(App\Models\Setting::class);
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                App\Models\Setting::set($k, $v);
            }

            return null;
        }

        return App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('perPage')) {
    /**
     * Get the pagination limit from tenant settings.
     *
     * @param  int  $default  Default number of items per page
     * @return int
     */
    function perPage(int $default = 15): int
    {
        $limit = (int) setting('pagination_limit', $default);

        // Ensure the limit is within allowed values
        $allowedLimits = [10, 15, 25, 50, 100];

        if (!in_array($limit, $allowedLimits)) {
            return $default;
        }

        return $limit;
    }
}

if (!function_exists('maxFileUpload')) {
    /**
     * Get the maximum file upload size from tenant settings (in kilobytes).
     *
     * Use in validation rules like: 'file' => 'required|file|max:' . maxFileUpload()
     *
     * @param  int  $default  Default size in megabytes
     * @return int  Size in kilobytes
     */
    function maxFileUpload(int $default = 10): int
    {
        $maxMB = (int) setting('max_file_upload', $default);

        // Ensure the limit is between 1 and 256 MB
        if ($maxMB < 1 || $maxMB > 256) {
            $maxMB = $default;
        }

        // Convert MB to KB for Laravel validation
        return $maxMB * 1024;
    }
}

if (!function_exists('maxFileUploadMB')) {
    /**
     * Get the maximum file upload size from tenant settings (in megabytes).
     *
     * Use for display purposes.
     *
     * @param  int  $default  Default size in megabytes
     * @return int  Size in megabytes
     */
    function maxFileUploadMB(int $default = 10): int
    {
        $maxMB = (int) setting('max_file_upload', $default);

        // Ensure the limit is between 1 and 256 MB
        if ($maxMB < 1 || $maxMB > 256) {
            return $default;
        }

        return $maxMB;
    }
}

if (!function_exists('currentCurrency')) {
    /**
     * Get the current default currency for the tenant.
     *
     * @return \App\Models\Currency|null
     */
    function currentCurrency()
    {
        return App\Models\Currency::getDefault();
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format an amount with the current currency symbol.
     *
     * @param  float  $amount
     * @param  \App\Models\Currency|null  $currency
     * @return string
     */
    function formatMoney(float $amount, $currency = null): string
    {
        if (!$currency) {
            $currency = currentCurrency();
        }

        if (!$currency) {
            return '$' . number_format($amount, 2);
        }

        return $currency->format($amount);
    }
}

if (!function_exists('convertCurrency')) {
    /**
     * Convert an amount from one currency to another.
     *
     * @param  float  $amount
     * @param  string  $fromCode  Currency code to convert from
     * @param  string  $toCode    Currency code to convert to
     * @return float
     */
    function convertCurrency(float $amount, string $fromCode, string $toCode): float
    {
        $fromCurrency = App\Models\Currency::where('code', $fromCode)->first();
        $toCurrency = App\Models\Currency::where('code', $toCode)->first();

        if (!$fromCurrency || !$toCurrency) {
            return $amount; // Return original amount if currencies not found
        }

        return $fromCurrency->convertTo($amount, $toCurrency);
    }
}

if (!function_exists('bankPaymentInstructions')) {
    /**
     * Get bank payment instructions for displaying to users.
     *
     * Returns bank details configured in Payment Settings if the bank_transfer
     * gateway is enabled, otherwise returns null.
     *
     * @return array|null  Array with bank details or null if not configured
     */
    function bankPaymentInstructions(): ?array
    {
        $setting = App\Models\PaymentGatewaySetting::where('gateway', 'bank_transfer')
            ->where('is_enabled', true)
            ->first();

        if (!$setting || empty($setting->config)) {
            return null;
        }

        return $setting->config;
    }
}
