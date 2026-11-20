<?php

namespace Database\Factories;

use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StockMovement> */
class StockMovementFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'product_batch_id' => ProductBatch::factory(),
            'storage_location_id' => StorageLocation::factory(),
            'type' => StockMovement::TYPE_RECEIPT,
            'quantity_milliunits' => 1_000,
            'occurred_at' => now(),
        ];
    }

    public function opening(int $costCents): static
    {
        return $this->state(fn (): array => [
            'type' => StockMovement::TYPE_OPENING,
            'cost_cents' => $costCents,
        ]);
    }
}
