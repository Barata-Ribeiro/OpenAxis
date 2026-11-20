<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Sluggable\Attributes\Sluggable;

#[Sluggable(from: 'name', maxLength: 180, preventOverwrite: true)]
#[Fillable(['unit_of_measure_id', 'storage_location_id', 'sku', 'name', 'description', 'cost_price_cents', 'sale_price_cents', 'minimum_stock_milliunits', 'is_active'])]
class Product extends Model implements Auditable
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $attributes = ['description' => ''];

    protected static function booted(): void
    {
        static::updating(function (self $product): void {
            if ($product->isDirty(['sku', 'unit_of_measure_id'])) {
                throw new DomainException('The SKU and base unit of an existing product cannot change.');
            }
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'cost_price_cents' => 'integer',
            'sale_price_cents' => 'integer',
            'minimum_stock_milliunits' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<UnitOfMeasure, $this> */
    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class)->withTrashed();
    }

    /** @return BelongsTo<StorageLocation, $this> */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class)->withTrashed();
    }

    /** @return BelongsToMany<Category, $this> */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /** @return MorphMany<Image, $this> */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order')->orderBy('id');
    }

    /** @return HasMany<ProductBatch, $this> */
    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    /** @return HasManyThrough<StockMovement, ProductBatch, $this> */
    public function stockMovements(): HasManyThrough
    {
        return $this->hasManyThrough(StockMovement::class, ProductBatch::class);
    }

    public function currentStockMilliunits(): int
    {
        return (int) $this->stockMovements()->sum('quantity_milliunits');
    }

    public function openingStockValueCents(): int
    {
        return (int) $this->stockMovements()->where('type', StockMovement::TYPE_OPENING)->sum('cost_cents');
    }

    /** @param Builder<self> $query */
    #[Scope]
    protected function search(Builder $query, string $term): void
    {
        $term = trim($term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $matches) use ($term): void {
            $matches->where('sku', $term);

            if (in_array($this->getConnection()->getDriverName(), ['mysql', 'pgsql'], true)) {
                $matches->orWhereFullText(['name', 'description'], $term, ['language' => 'simple']);

                return;
            }

            $matches->orWhereLike('name', '%'.$term.'%')
                ->orWhereLike('description', '%'.$term.'%');
        });
    }
}
