<?php

namespace App\Models;

use App\Enums\ShippedOrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $sales_orders_id
 * @property string $tracking_number
 * @property string $carrier
 * @property \Illuminate\Support\Carbon $shipped_date
 * @property ShippedOrderStatusEnum $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SalesOrder $salesOrder
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereCarrier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereSalesOrdersId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereShippedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ShippedOrder extends Model
{
    protected $fillable = [
        'sales_orders_id',
        'tracking_number',
        'carrier',
        'shipped_date',
        'status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'shipped_date' => 'date',
            'status' => ShippedOrderStatusEnum::class,
        ];
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_orders_id');
    }
}
