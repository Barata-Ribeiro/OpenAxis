<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'type' => Order::TYPE_SALE,
            'customer_id' => Customer::factory(),
            'ordered_at' => now(),
        ];
    }

    public function purchase(?Supplier $supplier = null): static
    {
        return $this->state(fn (): array => [
            'type' => Order::TYPE_PURCHASE,
            'customer_id' => null,
            'supplier_id' => $supplier?->getKey() ?? Supplier::factory(),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (): array => ['status' => Order::STATUS_CONFIRMED]);
    }
}
