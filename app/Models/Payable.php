<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $description
 * @property int $supplier_id
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $issue_date
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property string $status
 * @property string $payment_method
 * @property int $bank_account_id
 * @property string|null $reference_number
 * @property string|null $notes
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\BankAccount $bankAccount
 * @property-read \App\Models\Partner $supplier
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereUserId($value)
 * @mixin \Eloquent
 */
class Payable extends Model
{
    protected $table = 'payables';

    protected $fillable = [
        'description',
        'supplier_id',
        'amount',
        'issue_date',
        'due_date',
        'payment_date',
        'status',
        'payment_method',
        'bank_account_id',
        'reference_number',
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
            'issue_date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Partner::class, 'supplier_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
