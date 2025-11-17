<?php

namespace Database\Factories;

use App\Models\LandlordInvoice;
use App\Models\LandlordInvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandlordInvoiceItem>
 */
class LandlordInvoiceItemFactory extends Factory
{
    protected $model = LandlordInvoiceItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unit = $this->faker->numberBetween(100, 5000) / 100;

        return [
            'landlord_invoice_id' => LandlordInvoice::factory(),
            'line_type' => 'service',
            'description' => $this->faker->sentence(4),
            'category' => $this->faker->randomElement(['subscription', 'support', 'sms']),
            'quantity' => $quantity,
            'unit_price' => $unit,
            'line_total' => $quantity * $unit,
            'metadata' => [],
        ];
    }
}
