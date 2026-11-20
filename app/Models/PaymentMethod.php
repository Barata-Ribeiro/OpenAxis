<?php

namespace App\Models;

use Database\Factories\PaymentMethodFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperPaymentMethod
 */
#[Fillable(['name', 'fee_type', 'fee_rate', 'is_active'])]
class PaymentMethod extends Model implements Auditable
{
    /** @use HasFactory<PaymentMethodFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    public const string FEE_FIXED = 'fixed';

    public const string FEE_PERCENTAGE = 'percentage';

    protected $attributes = ['fee_type' => self::FEE_FIXED, 'fee_rate' => 0, 'is_active' => true];

    protected static function booted(): void
    {
        static::saving(function (self $method): void {
            self::validateFeeRate($method->fee_type, $method->fee_rate);
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fee_rate' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<CashEntry, $this> */
    public function cashEntries(): HasMany
    {
        return $this->hasMany(CashEntry::class);
    }

    public static function validateFeeRate(string $type, int $rate): void
    {
        if (! in_array($type, [self::FEE_FIXED, self::FEE_PERCENTAGE], true)
            || $rate < 0 || ($type === self::FEE_PERCENTAGE && $rate > 10_000)) {
            throw new DomainException('A fee requires a non-negative fixed amount or a percentage from zero to one hundred.');
        }
    }

    public static function calculateFeeCents(int $amountCents, string $type, int $rate): int
    {
        self::validateFeeRate($type, $rate);

        if ($amountCents < 0) {
            throw new DomainException('A fee requires a non-negative base amount.');
        }

        if ($type === self::FEE_FIXED) {
            return $rate;
        }

        $whole = intdiv($amountCents, 10_000) * $rate;
        $roundedRemainder = intdiv($amountCents % 10_000 * $rate + 5_000, 10_000);

        return $whole + $roundedRemainder;
    }
}
