<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Database\Factories\OrderItemFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['order_id', 'product_id', 'quantity_milliunits', 'unit_price_cents'])]
class OrderItem extends Model implements Auditable
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'quantity_milliunits' => 'integer',
            'unit_price_cents' => 'integer',
            'line_total_cents' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            if ($item->exists && $item->isDirty(['order_id', 'product_id', 'product_sku', 'product_name', 'unit_code'])) {
                throw new DomainException('An existing order item retains its order and product details.');
            }

            $quantity = $item->getAttribute('quantity_milliunits');
            $unitPrice = $item->getAttribute('unit_price_cents');

            if (! is_int($quantity) || $quantity <= 0 || ! is_int($unitPrice) || $unitPrice < 0) {
                throw new DomainException('An order item must have a positive quantity and a non-negative unit price.');
            }

            $product = $item->product()->firstOrFail();
            $unit = $product->unitOfMeasure()->firstOrFail();

            if (! $unit->allowsQuantity($quantity)) {
                throw new DomainException('The quantity exceeds the precision of the product unit.');
            }

            if (! $item->exists) {
                $item->product_sku = $product->sku;
                $item->product_name = $product->name;
                $item->unit_code = $unit->code;
            }

            $lineTotal = BigDecimal::of($unitPrice)
                ->multipliedBy($quantity)
                ->dividedBy(1000, 0, RoundingMode::HalfUp)
                ->toInt();
            $item->setAttribute('line_total_cents', $lineTotal);
        });
    }

    /** @param Builder<static> $query */
    protected function performUpdate(Builder $query): bool
    {
        if (! $this->isDirty(['quantity_milliunits', 'unit_price_cents'])) {
            return parent::performUpdate($query);
        }

        return $this->getConnection()->transaction(function () use ($query): bool {
            $current = $this->newQuery()->whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if ($current->stockMovements()->lockForUpdate()->first(['id']) !== null) {
                throw new DomainException('A posted order item retains its quantity and price.');
            }

            return parent::performUpdate($query);
        });
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /** @return HasMany<StockMovement, $this> */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
