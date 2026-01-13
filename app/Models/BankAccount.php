<?php

namespace App\Models;

use App\Enums\BankAccountTypeEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property BankAccountTypeEnum $type
 * @property string $bank_name
 * @property string $bank_agency
 * @property string $bank_account_number
 * @property string|null $pix_key Key for PIX transactions, if applicable.
 * @property string|null $destination_name Name of the account holder for transfers.
 * @property numeric $initial_balance
 * @property numeric $current_balance
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankAgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereCurrentBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereDestinationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereInitialBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount wherePixKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereUpdatedAt($value)
 *
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
        'pix_key',
        'destination_name',
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
            'type' => BankAccountTypeEnum::class,
            'initial_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
