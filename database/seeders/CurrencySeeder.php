<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            // Major World Currencies
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.000000, 'is_default' => true, 'is_active' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.850000, 'is_default' => false, 'is_active' => true],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.730000, 'is_default' => false, 'is_active' => true],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 110.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'exchange_rate' => 0.920000, 'is_default' => false, 'is_active' => true],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 1.250000, 'is_default' => false, 'is_active' => true],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.350000, 'is_default' => false, 'is_active' => true],

            // African Currencies
            ['code' => 'UGX', 'name' => 'Ugandan Shilling', 'symbol' => 'USh', 'exchange_rate' => 3700.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'exchange_rate' => 129.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'TZS', 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'exchange_rate' => 2300.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'exchange_rate' => 410.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'exchange_rate' => 15.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => 'GH₵', 'exchange_rate' => 6.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'RWF', 'name' => 'Rwandan Franc', 'symbol' => 'FRw', 'exchange_rate' => 1000.000000, 'is_default' => false, 'is_active' => true],

            // Asian Currencies
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 74.000000, 'is_default' => false, 'is_active' => true],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate' => 6.450000, 'is_default' => false, 'is_active' => true],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'exchange_rate' => 1.350000, 'is_default' => false, 'is_active' => true],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'exchange_rate' => 7.800000, 'is_default' => false, 'is_active' => true],

            // Middle East Currencies
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'exchange_rate' => 3.670000, 'is_default' => false, 'is_active' => true],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼', 'exchange_rate' => 3.750000, 'is_default' => false, 'is_active' => true],
        ];

        DB::connection('tenant')->table('currencies')->insert($currencies);
    }
}
