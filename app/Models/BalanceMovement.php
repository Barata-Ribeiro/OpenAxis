<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $bank_account_id
 * @property string $type
 * @property numeric $amount
 * @property string $movement_date
 * @property string $description
 * @property string|null $reference_number
 * @property int|null $destination_account_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount $bankAccount
 * @property-read \App\Models\BankAccount|null $destinationAccount
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereDestinationAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereMovementDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereUserId($value)
 *
 * @mixin \Eloquent
 */
class BalanceMovement extends Model
{
    protected $fillable = [
        'bank_account_id',
        'amount',
        'type',
        'description',
        'occurred_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
        ];
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function destinationAccount()
    {
        return $this->belongsTo(BankAccount::class, 'destination_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
