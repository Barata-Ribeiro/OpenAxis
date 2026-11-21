<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperCategory
 */
#[Fillable(['name', 'parent_id'])]
class Category extends Model implements Auditable
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (self $category): void {
            if ($category->exists && $category->isDirty('parent_id')) {
                $parent = $category->parent()->first();

                while ($parent !== null) {
                    if ($parent->is($category)) {
                        throw new DomainException('A category cannot be its own ancestor.');
                    }

                    $parent = $parent->parent()->first();
                }
            }
        });
    }

    /** @return BelongsTo<Category, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    /** @return HasMany<Category, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** @return BelongsToMany<Product, $this> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
