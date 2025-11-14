<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $purchase_order_id
 * @property int $product_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $subtotal_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\PurchaseOrder $purchaseOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereSubtotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemPurchaseOrder extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal_price',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal_price' => 'decimal:2',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
