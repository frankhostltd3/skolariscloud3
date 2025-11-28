<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\PayrollSetting;
use App\Http\Requests\PayrollSettingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PayrollSettingsController extends Controller
{
    public function index(): View
    {
        $settings = PayrollSetting::where('is_active', true)->get();
        $groupedSettings = PayrollSetting::getGroupedSettings();

        // Get current values for display
        $currentSettings = $settings->pluck('value', 'key')->toArray();

        return view('tenant.modules.human_resource.payroll_settings.index', compact('settings', 'groupedSettings', 'currentSettings'));
    }

    public function create(): View
    {
        $this->authorize('create', PayrollSetting::class);
        return view('tenant.modules.human_resource.payroll_settings.create');
    }

    public function store(PayrollSettingRequest $request): RedirectResponse
    {
        $this->authorize('create', PayrollSetting::class);

        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            PayrollSetting::setValue($key, $value);
        }

        return redirect()->route('tenant.modules.human-resource.payroll-settings.index')
                        ->with('success', 'Payroll settings updated successfully.');
    }

    public function show(PayrollSetting $payrollSetting): View
    {
        $this->authorize('view', $payrollSetting);
        return view('tenant.modules.human_resource.payroll_settings.show', compact('payrollSetting'));
    }

    public function edit(): View
    {
        $this->authorize('update', PayrollSetting::class);

        $groupedSettings = PayrollSetting::getGroupedSettings();

        // Inject pay frequency options if not present
        $configFrequencies = collect(config('payroll.frequencies', []));
        $frequencyOptions = $configFrequencies->mapWithKeys(fn($v, $k) => [$k => $v['label'] ?? ucfirst($k)])->toArray();
        if (empty($groupedSettings['pay_period']['pay_frequency']['options'] ?? [])) {
            $groupedSettings['pay_period']['pay_frequency']['options'] = $frequencyOptions;
            $groupedSettings['pay_period']['pay_frequency']['description'] = $groupedSettings['pay_period']['pay_frequency']['description']
                ?? 'How often employees are paid.';
        }

        // Get current values for form population
        $currentSettings = PayrollSetting::where('is_active', true)
                                        ->pluck('value', 'key')
                                        ->toArray();

        if (!isset($currentSettings['pay_frequency'])) {
            $currentSettings['pay_frequency'] = 'monthly';
        }

        // Inject currency options dynamically if missing
        if (empty($groupedSettings['currency']['default_currency']['options'] ?? [])) {
            // Try to derive from finance settings view style (currencies collection passed there)
            // Fallback to a concise ISO set if not available.
            $defaultCurrencyList = [
                'USD' => 'USD - US Dollar',
                'EUR' => 'EUR - Euro',
                'GBP' => 'GBP - British Pound',
                'UGX' => 'UGX - Ugandan Shilling',
                'KES' => 'KES - Kenyan Shilling',
                'TZS' => 'TZS - Tanzanian Shilling',
            ];

            // If a Setting holds a JSON array of currencies (e.g., supported_currencies), parse it.
            $supported = \App\Models\Setting::get('supported_currencies');
            if ($supported) {
                $decoded = is_string($supported) ? json_decode($supported, true) : $supported;
                if (is_array($decoded)) {
                    $mapped = [];
                    foreach ($decoded as $c) {
                        if (is_array($c) && isset($c['code'])) {
                            $mapped[$c['code']] = $c['code'] . (isset($c['name']) ? (' - ' . $c['name']) : '');
                        } elseif (is_string($c)) {
                            $mapped[$c] = $c;
                        }
                    }
                    if (!empty($mapped)) {
                        $defaultCurrencyList = $mapped;
                    }
                }
            }

            $groupedSettings['currency']['default_currency']['options'] = $defaultCurrencyList;
            $groupedSettings['currency']['default_currency']['description'] = $groupedSettings['currency']['default_currency']['description']
                ?? 'Select the base currency for payroll calculations.';
        }

        // Ensure current default currency valid
        if (!isset($currentSettings['default_currency'])) {
            $currentSettings['default_currency'] = 'USD';
        }

        // Inject payment method options if missing
        if (empty($groupedSettings['banking']['payment_method']['options'] ?? [])) {
            $groupedSettings['banking']['payment_method']['options'] = [
                'bank_transfer' => 'Bank Transfer',
                'check' => 'Check / Cheque',
                'cash' => 'Cash',
                'mobile_money' => 'Mobile Money',
                'direct_deposit' => 'Direct Deposit',
                'payroll_card' => 'Payroll Card',
            ];
            $groupedSettings['banking']['payment_method']['description'] = $groupedSettings['banking']['payment_method']['description']
                ?? 'Primary method for salary payments to employees.';
        }

        // Ensure current payment method valid
        if (!isset($currentSettings['payment_method'])) {
            $currentSettings['payment_method'] = 'bank_transfer';
        }

        // Get default currency from database (globally set in system settings)
        $defaultCurrency = \App\Models\Currency::getDefault();
        
        // Override payroll currency settings with system default currency
        if ($defaultCurrency) {
            $currentSettings['default_currency'] = $defaultCurrency->code;
            $currentSettings['currency_symbol'] = $defaultCurrency->symbol ?? currency_symbol($defaultCurrency->code);
        } else {
            // Derive currency symbol from selected default currency if not manually set
            if (empty($currentSettings['currency_symbol']) && !empty($currentSettings['default_currency'])) {
                $currentSettings['currency_symbol'] = $this->getCurrencySymbol($currentSettings['default_currency']);
            } elseif (empty($currentSettings['currency_symbol'])) {
                $currentSettings['currency_symbol'] = '$'; // Ultimate fallback
            }
        }

        return view('tenant.modules.human_resource.payroll_settings.edit', compact('groupedSettings', 'currentSettings'));
    }

    public function update(PayrollSettingRequest $request): RedirectResponse
    {
        $this->authorize('update', PayrollSetting::class);

        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            PayrollSetting::setValue($key, $value);
        }

        return redirect()->route('tenant.modules.human-resource.payroll-settings.index')
                        ->with('success', 'Payroll settings updated successfully.');
    }

    public function destroy(PayrollSetting $payrollSetting): RedirectResponse
    {
        $this->authorize('delete', $payrollSetting);
        $payrollSetting->delete();
        return redirect()->route('tenant.modules.human-resource.payroll-settings.index')
                        ->with('success', 'Payroll setting deleted successfully.');
    }

    /**
     * Reset settings to defaults
     */
    public function reset(Request $request): RedirectResponse
    {
        $this->authorize('update', PayrollSetting::class);

        // Default values from seeder
        $defaults = [
            'pay_frequency' => 'monthly',
            'pay_day' => '25',
            'fiscal_year_start' => '01-01',
            'default_currency' => 'USD',
            'currency_symbol' => '$',
            'decimal_places' => '2',
            'basic_salary_percentage' => '70',
            'house_allowance_percentage' => '15',
            'transport_allowance_percentage' => '10',
            'medical_allowance_percentage' => '5',
            'income_tax_rate' => '25',
            'social_security_rate' => '10',
            'provident_fund_rate' => '8',
            'overtime_rate_regular' => '1.5',
            'overtime_rate_holiday' => '2.0',
            'max_overtime_hours_monthly' => '40',
            'bank_name' => '',
            'bank_account_number' => '',
            'payment_method' => 'bank_transfer',
            'minimum_wage' => '50000',
            'working_hours_per_day' => '8',
            'working_days_per_week' => '5',
            'auto_process_payroll' => 'false',
            'email_pay_slips' => 'true',
            'export_to_accounting' => 'false'
        ];

        // Reset all settings to their default values
        foreach ($defaults as $key => $value) {
            PayrollSetting::setValue($key, $value);
        }

        return redirect()->route('tenant.modules.human-resource.payroll-settings.index')
                        ->with('success', 'Payroll settings reset to defaults.');
    }

    /**
     * Export settings
     */
    public function export(Request $request)
    {
        $this->authorize('view', PayrollSetting::class);

        $settings = PayrollSetting::where('is_active', true)->get();

        $data = $settings->map(function ($setting) {
            return [
                'Category' => $setting->category,
                'Key' => $setting->key,
                'Label' => $setting->label,
                'Value' => $setting->value,
                'Type' => $setting->type,
                'Description' => $setting->description,
            ];
        });

        return response()->json($data);
    }

    /**
     * Get currency symbol from currency code
     */
    protected function getCurrencySymbol(string $currencyCode): string
    {
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
