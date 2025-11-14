<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sales_order_id
 * @property int $product_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $subtotal_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\SalesOrder $salesOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereSalesOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereSubtotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemSalesOrder extends Model
{
    protected $fillable = [
        'sales_order_id',
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

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
