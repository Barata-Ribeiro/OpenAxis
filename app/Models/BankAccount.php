<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $bank_name
 * @property string $bank_agency
 * @property string $bank_account_number
 * @property numeric $initial_balance
 * @property numeric $current_balance
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankAgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereCurrentBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereInitialBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankAccount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'bank_name',
        'bank_agency',
        'bank_account_number',
        'initial_balance',
        'current_balance',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
