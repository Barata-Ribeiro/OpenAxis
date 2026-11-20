<?php

namespace App\Models;

use Database\Factories\FinancialAccountFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['type', 'order_id', 'expense_category_id', 'created_by_id', 'description', 'contact_name', 'contact_phone', 'amount_cents', 'due_on', 'cancelled_at', 'notes'])]
class FinancialAccount extends Model implements Auditable
{
    /** @use HasFactory<FinancialAccountFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    public const string TYPE_PAYABLE = 'payable';

    public const string TYPE_RECEIVABLE = 'receivable';

    public const string STATUS_PENDING = 'pending';

    public const string STATUS_PARTIALLY_PAID = 'partially_paid';

    public const string STATUS_PAID = 'paid';

    public const string STATUS_OVERDUE = 'overdue';

    public const string STATUS_CANCELLED = 'cancelled';

    protected static function booted(): void
    {
        static::saving(function (self $account): void {
            if ($account->amount_cents <= 0) {
                throw new DomainException('An obligation requires a positive amount.');
            }

            $validCategory = match ($account->getAttribute('type')) {
                self::TYPE_PAYABLE => $account->expense_category_id !== null,
                self::TYPE_RECEIVABLE => $account->expense_category_id === null,
                default => false,
            };

            if (! $validCategory) {
                throw new DomainException('An expense category is required for payables and unavailable for receivables.');
            }

            if ($account->exists && $account->isDirty(['type', 'order_id'])) {
                throw new DomainException('The type and order of an existing obligation cannot change.');
            }

            if ($account->order_id !== null) {
                $account->validateOrder();
            }
        });

        static::deleting(function (): never {
            throw new DomainException('Cancel an unsettled obligation to retain its history.');
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'due_on' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<ExpenseCategory, $this> */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class)->withTrashed();
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /** @return HasMany<CashEntry, $this> */
    public function cashEntries(): HasMany
    {
        return $this->hasMany(CashEntry::class);
    }

    /** @return Attribute<string, never> */
    protected function status(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->cancelled_at !== null) {
                return self::STATUS_CANCELLED;
            }

            $paid = $this->settledCents();

            return match (true) {
                $paid >= $this->amount_cents => self::STATUS_PAID,
                today()->isAfter($this->due_on) => self::STATUS_OVERDUE,
                $paid > 0 => self::STATUS_PARTIALLY_PAID,
                default => self::STATUS_PENDING,
            };
        });
    }

    public function settledCents(): int
    {
        return (int) $this->cashEntries()->sum('amount_cents');
    }

    public function outstandingCents(): int
    {
        return $this->cancelled_at === null ? $this->amount_cents - $this->settledCents() : 0;
    }

    public function lockAndGetSettledCents(): int
    {
        return (int) $this->cashEntries()
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('amount_cents')
            ->sum();
    }

    /** @param Builder<static> $query */
    protected function performUpdate(Builder $query): bool
    {
        if (! $this->isDirty(['amount_cents', 'cancelled_at'])) {
            return parent::performUpdate($query);
        }

        return $this->getConnection()->transaction(function () use ($query): bool {
            $current = $this->newQuery()->whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            $paid = $current->lockAndGetSettledCents();
            $cancelledAt = $this->isDirty('cancelled_at') ? $this->cancelled_at : $current->cancelled_at;

            if ($this->isDirty('amount_cents') && $this->amount_cents < $paid) {
                throw new DomainException('The obligation amount cannot be lower than its settlements.');
            }

            if ($cancelledAt !== null && $paid > 0) {
                throw new DomainException('An obligation with settlements cannot be cancelled.');
            }

            return parent::performUpdate($query);
        });
    }

    private function validateOrder(): void
    {
        $order = $this->order()->firstOrFail();

        if (($this->type === self::TYPE_PAYABLE) !== ($order->type === Order::TYPE_PURCHASE)) {
            throw new DomainException('The obligation type must match the purchase or sale it belongs to.');
        }

        if ($this->type === self::TYPE_RECEIVABLE) {
            if (! $this->exists && (blank($this->contact_name) || blank($this->contact_phone))) {
                $customer = $order->customer()->first();
                $this->contact_name = filled($this->contact_name) ? $this->contact_name : $customer?->name;
                $this->contact_phone = filled($this->contact_phone) ? $this->contact_phone : $customer?->phone;
            }

            if (blank($this->contact_name) || blank($this->contact_phone)) {
                throw new DomainException('A receivable for a sale requires a customer name and phone.');
            }
        }
    }
}
