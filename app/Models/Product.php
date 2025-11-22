<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Str;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property numeric $cost_price Cost price of the product
 * @property numeric $selling_price Selling price of the product
 * @property int $current_stock Current stock level of the product
 * @property int $minimum_stock Minimum stock level of the product
 * @property numeric $comission Commission percentage for the product
 * @property bool $is_active
 * @property int $product_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read bool|null $audits_exists
 * @property-read \App\Models\ProductCategory|null $category
 * @property-read mixed $cover_image
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read bool|null $media_exists
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereComission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCurrentStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMinimumStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model implements Auditable, HasMedia
{
    use InteractsWithMedia, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'slug',
        'cost_price',
        'selling_price',
        'current_stock',
        'minimum_stock',
        'comission',
        'is_active',
        'product_category_id',
    ];

    protected $appends = ['cover_image'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['media'];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'comission' => 'decimal:0',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug) && ! empty($product->name)) {
                $base = Str::slug($product->name);
                $slug = $base;
                $i = 1;

                while (static::withTrashed()->whereSlug($slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }

                $product->slug = $slug;
            }
        });
    }

    public function getCoverImageAttribute()
    {
        $media = $this->getMedia('products_images', ['is_cover' => true]);

        return $media->isNotEmpty() ? $media->firstOrFail() : null;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function registerMediaCollections(?Media $media = null): void
    {
        $this->addMediaCollection('products_images')
            ->onlyKeepLatest(3)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
