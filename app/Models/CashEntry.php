<?php

namespace App\Models;

use Database\Factories\CashEntryFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperCashEntry
 */
#[Fillable(['type', 'financial_account_id', 'expense_category_id', 'payment_method_id', 'created_by_id', 'description', 'amount_cents', 'occurred_at', 'notes'])]
class CashEntry extends Model implements Auditable
{
    /** @use HasFactory<CashEntryFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    public const string TYPE_RECEIPT = 'receipt';

    public const string TYPE_EXPENSE = 'expense';

    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new DomainException('Posted cash entries cannot be edited.');
        });

        static::deleting(function (): never {
            throw new DomainException('Posted cash entries cannot be deleted.');
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'fee_rate' => 'integer',
            'fee_cents' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<FinancialAccount, $this> */
    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    /** @return BelongsTo<ExpenseCategory, $this> */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class)->withTrashed();
    }

    /** @return BelongsTo<PaymentMethod, $this> */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class)->withTrashed();
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /** @param Builder<static> $query */
    protected function performInsert(Builder $query): bool
    {
        return $this->getConnection()->transaction(function () use ($query): bool {
            if ($this->amount_cents <= 0 || ! in_array($this->getAttribute('type'), [self::TYPE_RECEIPT, self::TYPE_EXPENSE], true)) {
                throw new DomainException('A cash entry requires a positive receipt or expense amount.');
            }

            $manualExpense = $this->financial_account_id === null && $this->type === self::TYPE_EXPENSE;

            if ($manualExpense !== ($this->expense_category_id !== null)) {
                throw new DomainException('An expense category is required for manual expenses only.');
            }

            if ($this->financial_account_id !== null) {
                $this->validateSettlement();
            }

            $method = $this->paymentMethod()->sharedLock()->firstOrFail();

            if ($method->trashed() || ! $method->is_active) {
                throw new DomainException('The payment method is not available for new entries.');
            }

            $this->forceFill([
                'fee_type' => $method->fee_type,
                'fee_rate' => $method->fee_rate,
                'fee_cents' => PaymentMethod::calculateFeeCents($this->amount_cents, $method->fee_type, $method->fee_rate),
            ]);

            return parent::performInsert($query);
        });
    }

    private function validateSettlement(): void
    {
        $account = $this->financialAccount()->lockForUpdate()->firstOrFail();

        if (($this->type === self::TYPE_EXPENSE) !== ($account->type === FinancialAccount::TYPE_PAYABLE)) {
            throw new DomainException('A payable must be settled by an expense and a receivable by a receipt.');
        }

        if ($account->cancelled_at !== null
            || $this->amount_cents > $account->amount_cents - $account->lockAndGetSettledCents()) {
            throw new DomainException('The settlement exceeds the outstanding amount.');
        }
    }
}
