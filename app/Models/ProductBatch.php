<?php

namespace App\Models;

use Database\Factories\ProductBatchFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperProductBatch
 */
#[Fillable(['product_id', 'supplier_lot_code', 'expires_on'])]
class ProductBatch extends Model implements Auditable
{
    /** @use HasFactory<ProductBatchFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'expires_on' => 'date',
            'sequence' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $batch): void {
            if ($batch->isDirty(['product_id', 'sequence', 'code'])) {
                throw new DomainException('The product and identifiers of an existing batch cannot change.');
            }
        });

        static::deleting(function (): never {
            throw new DomainException('Product batches are retained for traceability.');
        });
    }

    /** @param Builder<static> $query */
    protected function performInsert(Builder $query): bool
    {
        return $this->getConnection()->transaction(function () use ($query): bool {
            $product = $this->product()->lockForUpdate()->firstOrFail();
            $previousSequence = $product->batches()->orderByDesc('sequence')->lockForUpdate()->value('sequence');

            $sequence = (int) $previousSequence + 1;
            $this->setAttribute('sequence', $sequence);
            $this->code = sprintf('%s-%03d', $product->sku, $sequence);

            return parent::performInsert($query);
        });
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
