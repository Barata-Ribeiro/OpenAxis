<?php

namespace App\Models;

use Database\Factories\StockMovementFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperStockMovement
 */
#[Fillable(['product_batch_id', 'storage_location_id', 'order_item_id', 'return_of_id', 'created_by_id', 'type', 'quantity_milliunits', 'cost_cents', 'occurred_at', 'notes'])]
class StockMovement extends Model implements Auditable
{
    /** @use HasFactory<StockMovementFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    public const string TYPE_OPENING = 'opening';

    public const string TYPE_RECEIPT = 'receipt';

    public const string TYPE_ISSUE = 'issue';

    public const string TYPE_RETURN = 'return';

    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new DomainException('Posted stock movements cannot be edited.');
        });

        static::deleting(function (): never {
            throw new DomainException('Posted stock movements cannot be deleted.');
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'product_batch_id' => 'integer',
            'order_item_id' => 'integer',
            'quantity_milliunits' => 'integer',
            'cost_cents' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<ProductBatch, $this> */
    public function productBatch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class);
    }

    /** @return BelongsTo<StorageLocation, $this> */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class)->withTrashed();
    }

    /** @return BelongsTo<OrderItem, $this> */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /** @return BelongsTo<StockMovement, $this> */
    public function originalMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'return_of_id');
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /** @return HasMany<StockMovement, $this> */
    public function returns(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'return_of_id');
    }

    /** @param Builder<static> $query */
    protected function performInsert(Builder $query): bool
    {
        return $this->getConnection()->transaction(function () use ($query): bool {
            $batch = $this->productBatch()->firstOrFail();
            $product = $batch->product()->lockForUpdate()->firstOrFail();
            $unit = $product->unitOfMeasure()->firstOrFail();
            $quantity = $this->getAttribute('quantity_milliunits');

            if (! is_int($quantity) || $quantity === 0 || $quantity === PHP_INT_MIN || ! $unit->allowsQuantity($quantity)) {
                throw new DomainException('The stock movement requires a non-zero quantity in the product unit.');
            }

            $this->validateDirectionAndReturn($batch);

            if ($this->order_item_id !== null) {
                $this->validateOrderItem($product);
            }

            if ($this->quantity_milliunits < 0) {
                $available = $batch->stockMovements()
                    ->where('storage_location_id', $this->storage_location_id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->pluck('quantity_milliunits')
                    ->sum();

                if ($available < -$this->quantity_milliunits) {
                    throw new DomainException('This batch has insufficient stock at the selected location.');
                }
            }

            return parent::performInsert($query);
        });
    }

    private function validateDirectionAndReturn(ProductBatch $batch): void
    {
        $quantity = $this->quantity_milliunits;
        $cost = $this->getAttribute('cost_cents');
        $valid = match ($this->getAttribute('type')) {
            self::TYPE_OPENING => $quantity > 0 && $this->order_item_id === null && $cost !== null,
            self::TYPE_RECEIPT => $quantity > 0,
            self::TYPE_ISSUE => $quantity < 0,
            self::TYPE_RETURN => $this->return_of_id !== null,
            default => false,
        };

        if (! $valid || ($cost !== null && (! is_int($cost) || $cost < 0))
            || ($this->type !== self::TYPE_RETURN && $this->return_of_id !== null)) {
            throw new DomainException('The stock movement has an invalid direction, cost, or source.');
        }

        if ($this->type !== self::TYPE_RETURN) {
            return;
        }

        $original = $this->originalMovement()->firstOrFail();

        if ($original->product_batch_id !== $batch->getKey()
            || ! in_array($original->type, [self::TYPE_RECEIPT, self::TYPE_ISSUE], true)
            || ($original->quantity_milliunits > 0) === ($quantity > 0)
            || ($this->order_item_id !== null && $this->order_item_id !== $original->order_item_id)) {
            throw new DomainException('A return must reverse a receipt or issue for the same batch and order item.');
        }

        $previouslyReturned = (int) $original->returns()->orderBy('id')->lockForUpdate()->pluck('quantity_milliunits')->sum();

        if (abs($quantity) > abs($original->quantity_milliunits) - abs($previouslyReturned)) {
            throw new DomainException('The return exceeds the quantity remaining on the original movement.');
        }

        $this->order_item_id = $original->order_item_id;
    }

    private function validateOrderItem(Product $product): void
    {
        $item = $this->orderItem()->lockForUpdate()->firstOrFail();
        $order = $item->order()->firstOrFail();

        if ($item->product_id !== $product->getKey()
            || ! in_array($order->status, [Order::STATUS_CONFIRMED, Order::STATUS_COMPLETED], true)
            || ($this->type === self::TYPE_RECEIPT && $order->type !== Order::TYPE_PURCHASE)
            || ($this->type === self::TYPE_ISSUE && $order->type !== Order::TYPE_SALE)) {
            throw new DomainException('The movement must match the product and direction of a confirmed order item.');
        }

        if ($this->type === self::TYPE_RETURN) {
            return;
        }

        $posted = (int) $item->stockMovements()
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('quantity_milliunits')
            ->sum();

        if (abs($this->quantity_milliunits) > $item->quantity_milliunits - abs($posted)) {
            throw new DomainException('The stock movement would exceed the ordered quantity.');
        }
    }
}
