<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperOrder
 */
#[Fillable(['type', 'status', 'customer_id', 'supplier_id', 'created_by_id', 'ordered_at', 'external_reference', 'delivery_fee_type', 'delivery_fee_rate', 'notes'])]
class Order extends Model implements Auditable
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, HasUlids, \OwenIt\Auditing\Auditable;

    public const string TYPE_PURCHASE = 'purchase';

    public const string TYPE_SALE = 'sale';

    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_CONFIRMED = 'confirmed';

    public const string STATUS_COMPLETED = 'completed';

    public const string STATUS_CANCELLED = 'cancelled';

    protected $attributes = ['status' => self::STATUS_DRAFT, 'delivery_fee_rate' => 0];

    protected static function booted(): void
    {
        static::saving(function (self $order): void {
            $validParty = match ($order->getAttribute('type')) {
                self::TYPE_PURCHASE => $order->supplier_id !== null && $order->customer_id === null,
                self::TYPE_SALE => $order->supplier_id === null,
                default => false,
            };

            if (! $validParty) {
                throw new DomainException('Purchases require a supplier. Sales may identify a customer.');
            }

            if ($order->exists && $order->isDirty(['number', 'type'])) {
                throw new DomainException('The number and type of an existing order cannot change.');
            }

            if ($order->delivery_fee_type === null && $order->delivery_fee_rate !== 0) {
                throw new DomainException('A delivery fee rate requires a delivery fee type.');
            }

            if ($order->delivery_fee_type !== null) {
                PaymentMethod::validateFeeRate($order->delivery_fee_type, $order->delivery_fee_rate);
            }
        });
    }

    /** @return list<string> */
    public function uniqueIds(): array
    {
        return ['number'];
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'delivery_fee_rate' => 'integer',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasMany<FinancialAccount, $this> */
    public function financialAccounts(): HasMany
    {
        return $this->hasMany(FinancialAccount::class);
    }

    public function reference(): string
    {
        return '#'.$this->number;
    }

    public function subtotalCents(): int
    {
        return (int) $this->items()->sum('line_total_cents');
    }

    public function deliveryFeeCents(): int
    {
        return $this->feeOn($this->subtotalCents());
    }

    public function totalCents(): int
    {
        $subtotal = $this->subtotalCents();

        return $subtotal + $this->feeOn($subtotal);
    }

    private function feeOn(int $subtotalCents): int
    {
        return $this->delivery_fee_type === null
            ? 0
            : PaymentMethod::calculateFeeCents($subtotalCents, $this->delivery_fee_type, $this->delivery_fee_rate);
    }
}
