<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $client_id
 * @property int $vendor_id
 * @property string $order_number
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon|null $delivery_date
 * @property string $status
 * @property numeric $product_cost Cost of the products in the sales order
 * @property numeric $delivery_cost Cost of delivery for the sales order
 * @property numeric $discount_cost Discount applied to the sales order
 * @property numeric $total_cost Total cost of the sales order
 * @property numeric $product_value Total value of the products in the sales order
 * @property numeric $total_commission Total commission for the sales order
 * @property string $payment_method
 * @property string|null $notes
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $payment_condition_id
 * @property-read \App\Models\Partner $client
 * @property-read \App\Models\PaymentCondition|null $paymentCondition
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemSalesOrder> $salesOrderItems
 * @property-read int|null $sales_order_items_count
 * @property-read bool|null $sales_order_items_exists
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDeliveryCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDiscountCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder wherePaymentConditionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereProductCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereProductValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereTotalCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereVendorId($value)
 * @mixin \Eloquent
 */
class SalesOrder extends Model
{
    protected $fillable = [
        'client_id',
        'vendor_id',
        'payment_condition_id',
        'order_number',
        'order_date',
        'delivery_date',
        'status',
        'product_cost',
        'delivery_cost',
        'discount_cost',
        'total_cost',
        'product_value',
        'total_commission',
        'payment_method',
        'notes',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
            'product_cost' => 'decimal:2',
            'delivery_cost' => 'decimal:2',
            'discount_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'product_value' => 'decimal:2',
            'total_commission' => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Partner::class, 'client_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function paymentCondition()
    {
        return $this->belongsTo(PaymentCondition::class, 'payment_condition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesOrderItems()
    {
        return $this->hasMany(ItemSalesOrder::class);
    }
}
