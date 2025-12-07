<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code Unique code representing the payment condition.
 * @property string $name Name of the payment condition.
 * @property int $days_until_due Number of days until the payment is due.
 * @property int $installments Number of installments for the payment condition.
 * @property bool $is_active Indicates whether the payment condition is currently active.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereDaysUntilDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentCondition whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PaymentCondition extends Model
{
    protected $fillable = [
        'code',
        'name',
        'days_until_due',
        'installments',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'days_until_due' => 'integer',
            'installments' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName()
    {
        return 'code';
    }
}
