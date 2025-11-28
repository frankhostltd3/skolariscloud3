<?php

namespace Database\Seeders;

use App\Models\PayrollSetting;
use Illuminate\Database\Seeder;

class PayrollSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'pay_period' => [
                [
                    'key' => 'pay_frequency',
                    'label' => 'Pay Frequency',
                    'type' => 'select',
                    'value' => 'monthly',
                    'description' => 'How often employees receive their salary payments.',
                    'validation_rules' => ['required', 'in:monthly,semi_monthly,bi_weekly,weekly'],
                    'options' => [
                        'monthly' => 'Monthly',
                        'semi_monthly' => 'Semi-Monthly (twice a month)',
                        'bi_weekly' => 'Bi-Weekly (every two weeks)',
                        'weekly' => 'Weekly',
                    ],
                ],
                [
                    'key' => 'pay_day',
                    'label' => 'Pay Day',
                    'type' => 'number',
                    'value' => 25,
                    'description' => 'Calendar day that payroll is processed (1 - 31).',
                    'validation_rules' => ['nullable', 'integer', 'between:1,31'],
                ],
                [
                    'key' => 'fiscal_year_start',
                    'label' => 'Fiscal Year Start',
                    'type' => 'text',
                    'value' => '01-01',
                    'description' => 'Month and day when the new fiscal year begins (MM-DD).',
                    'validation_rules' => ['nullable', 'regex:/^\d{2}-\d{2}$/'],
                ],
            ],
            'currency' => [
                [
                    'key' => 'default_currency',
                    'label' => 'Default Currency',
                    'type' => 'select',
                    'value' => 'USD',
                    'description' => 'Primary currency used for payroll calculations and reporting.',
                    'validation_rules' => ['required', 'string', 'size:3'],
                    'options' => [
                        'USD' => 'USD - US Dollar',
                        'EUR' => 'EUR - Euro',
                        'GBP' => 'GBP - British Pound',
                        'UGX' => 'UGX - Ugandan Shilling',
                        'KES' => 'KES - Kenyan Shilling',
                        'TZS' => 'TZS - Tanzanian Shilling',
                        'ZAR' => 'ZAR - South African Rand',
                        'NGN' => 'NGN - Nigerian Naira',
                        'GHS' => 'GHS - Ghanaian Cedi',
                    ],
                ],
                [
                    'key' => 'currency_symbol',
                    'label' => 'Currency Symbol',
                    'type' => 'text',
                    'value' => '$',
                    'description' => 'Symbol displayed on payslips and payroll reports (auto-derived if left blank).',
                    'validation_rules' => ['nullable', 'string', 'max:5'],
                ],
                [
                    'key' => 'decimal_places',
                    'label' => 'Decimal Places',
                    'type' => 'number',
                    'value' => 2,
                    'description' => 'Number of decimal places to use when formatting salary amounts.',
                    'validation_rules' => ['nullable', 'integer', 'between:0,4'],
                ],
            ],
            'salary_components' => [
                [
                    'key' => 'basic_salary_percentage',
                    'label' => 'Basic Salary (%)',
                    'type' => 'number',
                    'value' => 70,
                    'description' => 'Percentage of total salary allocated to the basic salary component.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
                [
                    'key' => 'house_allowance_percentage',
                    'label' => 'House Allowance (%)',
                    'type' => 'number',
                    'value' => 15,
                    'description' => 'Percentage allocation for housing allowance.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
                [
                    'key' => 'transport_allowance_percentage',
                    'label' => 'Transport Allowance (%)',
                    'type' => 'number',
                    'value' => 10,
                    'description' => 'Percentage allocation for transport allowance.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
                [
                    'key' => 'medical_allowance_percentage',
                    'label' => 'Medical Allowance (%)',
                    'type' => 'number',
                    'value' => 5,
                    'description' => 'Percentage allocation for medical allowance.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
            ],
            'deductions' => [
                [
                    'key' => 'income_tax_rate',
                    'label' => 'Income Tax Rate (%)',
                    'type' => 'number',
                    'value' => 25,
                    'description' => 'Default income tax rate applied to taxable salary components.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
                [
                    'key' => 'social_security_rate',
                    'label' => 'Social Security Rate (%)',
                    'type' => 'number',
                    'value' => 10,
                    'description' => 'Employer contribution to national social security funds.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
                [
                    'key' => 'provident_fund_rate',
                    'label' => 'Provident Fund Rate (%)',
                    'type' => 'number',
                    'value' => 8,
                    'description' => 'Contribution allocated to provident or pension funds.',
                    'validation_rules' => ['nullable', 'numeric', 'between:0,100'],
                ],
            ],
            'overtime' => [
                [
                    'key' => 'overtime_rate_regular',
                    'label' => 'Regular Overtime Rate (x)',
                    'type' => 'number',
                    'value' => 1.5,
                    'description' => 'Multiplier applied to standard hourly rate for overtime hours.',
                    'validation_rules' => ['nullable', 'numeric', 'between:1,5'],
                ],
                [
                    'key' => 'overtime_rate_holiday',
                    'label' => 'Holiday Overtime Rate (x)',
                    'type' => 'number',
                    'value' => 2.0,
                    'description' => 'Multiplier applied on public holidays or rest days.',
                    'validation_rules' => ['nullable', 'numeric', 'between:1,5'],
                ],
                [
                    'key' => 'max_overtime_hours_monthly',
                    'label' => 'Max Overtime Hours (Monthly)',
                    'type' => 'number',
                    'value' => 40,
                    'description' => 'Maximum overtime hours allowed per employee each month.',
                    'validation_rules' => ['nullable', 'integer', 'between:0,200'],
                ],
            ],
            'banking' => [
                [
                    'key' => 'bank_name',
                    'label' => 'Bank Name',
                    'type' => 'text',
                    'value' => '',
                    'description' => 'Primary bank that handles payroll disbursements.',
                    'validation_rules' => ['nullable', 'string', 'max:255'],
                ],
                [
                    'key' => 'bank_account_number',
                    'label' => 'Bank Account Number',
                    'type' => 'text',
                    'value' => '',
                    'description' => 'Account number used for payroll transfers.',
                    'validation_rules' => ['nullable', 'string', 'max:64'],
                ],
                [
                    'key' => 'payment_method',
                    'label' => 'Default Payment Method',
                    'type' => 'select',
                    'value' => 'bank_transfer',
                    'description' => 'Preferred method for paying employees.',
                    'validation_rules' => ['nullable', 'string'],
                    'options' => [
                        'bank_transfer' => 'Bank Transfer',
                        'check' => 'Check / Cheque',
                        'cash' => 'Cash',
                        'mobile_money' => 'Mobile Money',
                        'direct_deposit' => 'Direct Deposit',
                        'payroll_card' => 'Payroll Card',
                    ],
                ],
            ],
            'compliance' => [
                [
                    'key' => 'minimum_wage',
                    'label' => 'Minimum Monthly Wage',
                    'type' => 'number',
                    'value' => 50000,
                    'description' => 'Legally mandated minimum wage for full-time employees.',
                    'validation_rules' => ['nullable', 'numeric', 'min:0'],
                ],
                [
                    'key' => 'working_hours_per_day',
                    'label' => 'Working Hours Per Day',
                    'type' => 'number',
                    'value' => 8,
                    'description' => 'Standard number of working hours per day.',
                    'validation_rules' => ['nullable', 'integer', 'between:1,24'],
                ],
                [
                    'key' => 'working_days_per_week',
                    'label' => 'Working Days Per Week',
                    'type' => 'number',
                    'value' => 5,
                    'description' => 'Number of working days per week.',
                    'validation_rules' => ['nullable', 'integer', 'between:1,7'],
                ],
            ],
            'integration' => [
                [
                    'key' => 'auto_process_payroll',
                    'label' => 'Auto Process Payroll',
                    'type' => 'boolean',
                    'value' => false,
                    'description' => 'Automatically queue payroll processing jobs when pay day is reached.',
                    'validation_rules' => ['nullable', 'boolean'],
                ],
                [
                    'key' => 'email_pay_slips',
                    'label' => 'Email Pay Slips',
                    'type' => 'boolean',
                    'value' => true,
                    'description' => 'Send pay slips to employees by email after payroll is processed.',
                    'validation_rules' => ['nullable', 'boolean'],
                ],
                [
                    'key' => 'export_to_accounting',
                    'label' => 'Export to Accounting',
                    'type' => 'boolean',
                    'value' => false,
                    'description' => 'Generate export files for accounting platforms after payroll completion.',
                    'validation_rules' => ['nullable', 'boolean'],
                ],
                [
                    'key' => 'export_format',
                    'label' => 'Export Format',
                    'type' => 'select',
                    'value' => 'csv',
                    'description' => 'Default file format used when exporting payroll data.',
                    'validation_rules' => ['nullable', 'in:csv,json,xml'],
                    'options' => [
                        'csv' => 'CSV (Spreadsheet)',
                        'json' => 'JSON',
                        'xml' => 'XML',
                    ],
                ],
            ],
        ];

        foreach ($settings as $category => $items) {
            foreach ($items as $index => $definition) {
                PayrollSetting::updateOrCreate(
                    ['key' => $definition['key']],
                    array_merge(
                        [
                            'category' => $category,
                            'sort_order' => ($index + 1) * 10,
                            'is_active' => true,
                        ],
                        $definition
                    )
                );
            }
        }
    }
}
