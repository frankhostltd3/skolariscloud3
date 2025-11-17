<?php

if (!function_exists('money')) {
    /**
     * Format a number as currency
     *
     * @param float|int $amount
     * @param string|null $currency
     * @param bool $withCode
     * @return string
     */
    function money($amount, ?string $currency = null, bool $withCode = false): string
    {
        $amount = (float) $amount;

        // Get currency from settings or use default
        if (!$currency) {
            $currency = setting('currency', 'USD');
        }

        // Format the number
        $formatted = number_format($amount, 2);

        if ($withCode) {
            return $currency . ' ' . $formatted;
        }

        return $formatted;
    }
}

if (!function_exists('curriculum_classes')) {
    /**
     * Get curriculum classes helper
     *
     * @return mixed
     */
    function curriculum_classes()
    {
        return \App\Models\Academic\ClassRoom::distinct('grade_level')->pluck('grade_level')->filter()->values();
    }
}

if (!function_exists('default_currency')) {
    /**
     * Get the default currency for the tenant
     *
     * @return \App\Models\Currency|null
     */
    function default_currency()
    {
        return \App\Models\Currency::getDefault();
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get the active currency code for the tenant.
     */
    function currency_code(): string
    {
        $currency = default_currency();

        if ($currency) {
            return $currency->code;
        }

        return (string) setting('currency', config('app.currency', 'USD'));
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get the currency symbol for the default currency or a specific currency code
     *
     * @param string|null $currencyCode
     * @return string
     */
    function currency_symbol(?string $currencyCode = null): string
    {
        // If no code provided, get from default currency
        if (!$currencyCode) {
            $defaultCurrency = default_currency();
            if ($defaultCurrency) {
                return $defaultCurrency->symbol ?? $defaultCurrency->code;
            }
            // Fallback to setting or config
            $currencyCode = setting('currency', config('app.currency', 'USD'));
        }

        // Currency symbol mapping
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'UGX' => 'USh',
            'KES' => 'KSh',
            'TZS' => 'TSh',
            'ZAR' => 'R',
            'NGN' => '₦',
            'GHS' => '₵',
            'RWF' => 'FRw',
            'JPY' => '¥',
            'CNY' => '¥',
            'INR' => '₹',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'PLN' => 'zł',
            'CZK' => 'Kč',
            'HUF' => 'Ft',
            'RUB' => '₽',
            'BRL' => 'R$',
            'MXN' => 'MX$',
            'AED' => 'د.إ',
            'SAR' => '﷼',
            'THB' => '฿',
            'SGD' => 'S$',
            'MYR' => 'RM',
            'PHP' => '₱',
            'IDR' => 'Rp',
            'VND' => '₫',
            'KRW' => '₩',
            'TRY' => '₺',
            'ILS' => '₪',
            'EGP' => 'E£',
            'MAD' => 'د.م.',
            'PKR' => '₨',
            'BDT' => '৳',
            'LKR' => 'Rs',
            'NPR' => 'रू',
        ];

        return $symbols[strtoupper($currencyCode)] ?? strtoupper($currencyCode);
    }
}

if (!function_exists('format_money')) {
    /**
     * Format amount with currency symbol
     *
     * @param float|int $amount
     * @param string|null $currencyCode
     * @param bool $showCode
     * @return string
     */
    function format_money($amount, ?string $currencyCode = null, bool $showCode = false): string
    {
        $amount = (float) $amount;
        
        // Get currency
        if (!$currencyCode) {
            $currency = default_currency();
        } else {
            $currency = \App\Models\Currency::where('code', $currencyCode)->first();
        }

        if (!$currency) {
            // Fallback formatting
            $symbol = currency_symbol($currencyCode);
            $formatted = number_format($amount, 2);
            return $symbol . ' ' . $formatted;
        }

        // Format with currency settings
        $formatted = number_format(
            $amount,
            $currency->decimal_places ?? 2,
            $currency->decimal_separator ?? '.',
            $currency->thousands_separator ?? ','
        );

        $symbol = $currency->symbol ?? currency_symbol($currency->code);
        
        if ($currency->symbol_position === 'after') {
            $result = $formatted . ' ' . $symbol;
        } else {
            $result = $symbol . ' ' . $formatted;
        }

        if ($showCode) {
            $result .= ' (' . $currency->code . ')';
        }

        return $result;
    }
}

if (!function_exists('school_logo')) {
    /**
     * Get the school logo URL for the current tenant
     *
     * @param bool $absolute Return absolute URL
     * @return string|null
     */
    function school_logo(bool $absolute = true): ?string
    {
        // Try to get from School model first
        $school = \App\Models\School::query()->first();
        if ($school && $school->logo_path) {
            return $absolute 
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($school->logo_path)
                : $school->logo_path;
        }

        // Fallback to settings
        $logoPath = setting('school_logo');
        if ($logoPath) {
            return $absolute
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath)
                : $logoPath;
        }

        return null;
    }
}

if (!function_exists('school_logo_path')) {
    /**
     * Get the absolute file system path to the school logo
     *
     * @return string|null
     */
    function school_logo_path(): ?string
    {
        $logoPath = school_logo(false);
        if (!$logoPath) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->path($logoPath);
    }
}