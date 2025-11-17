<?php

namespace Database\Factories;

use App\Models\LandlordInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandlordInvoice>
 */
class LandlordInvoiceFactory extends Factory
{
    protected $model = LandlordInvoice::class;

    public function definition(): array
    {
        $issued = $this->faker->dateTimeBetween('-1 month', 'now');
        $due = (clone $issued)->modify('+14 days');

        return [
            'invoice_number' => LandlordInvoice::generateNextInvoiceNumber(),
            'tenant_id' => null,
            'tenant_name_snapshot' => $this->faker->company(),
            'tenant_plan_snapshot' => $this->faker->randomElement(['Starter', 'Growth', 'Enterprise']),
            'status' => 'pending',
            'auto_generated' => false,
            'issued_at' => $issued,
            'due_at' => $due,
            'period_start' => (clone $issued)->modify('-14 days'),
            'period_end' => (clone $due),
            'subtotal' => 0,
            'tax_total' => 0,
            'discount_total' => 0,
            'total' => 0,
            'balance_due' => 0,
            'metadata' => [],
        ];
    }
}
