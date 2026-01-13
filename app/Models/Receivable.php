<?php

namespace App\Models;

use App\Enums\ReceivableStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $code
 * @property string $description
 * @property int $client_id
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $issue_date
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon|null $received_date
 * @property ReceivableStatusEnum $status
 * @property string $payment_method
 * @property int $bank_account_id
 * @property int $sales_order_id
 * @property string|null $reference_number
 * @property string|null $notes
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount $bankAccount
 * @property-read \App\Models\Partner $client
 * @property-read \App\Models\SalesOrder $salesOrder
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereSalesOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Receivable extends Model
{
    protected $fillable = [
        'code',
        'description',
        'client_id',
        'amount',
        'issue_date',
        'due_date',
        'received_date',
        'status',
        'payment_method',
        'bank_account_id',
        'sales_order_id',
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
            'amount' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'received_date' => 'date',
            'status' => ReceivableStatusEnum::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'client_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
