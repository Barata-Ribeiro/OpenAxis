<?php

namespace App\Models;

use Database\Factories\UnitOfMeasureFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['code', 'name', 'quantity_decimals'])]
class UnitOfMeasure extends Model implements Auditable
{
    /** @use HasFactory<UnitOfMeasureFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'quantity_decimals' => 'integer',
        ];
    }

    protected $attributes = ['quantity_decimals' => 0];

    protected static function booted(): void
    {
        static::saving(function (self $unit): void {
            $decimals = $unit->getAttribute('quantity_decimals');

            if (! is_int($decimals) || $decimals < 0 || $decimals > 3) {
                throw new DomainException('A unit must allow between zero and three decimal places.');
            }

            if ($unit->exists && $unit->isDirty(['code', 'quantity_decimals'])) {
                throw new DomainException('The code and precision of an existing unit cannot change.');
            }
        });
    }

    public function allowsQuantity(int $quantityMilliunits): bool
    {
        return $quantityMilliunits % (10 ** (3 - $this->quantity_decimals)) === 0;
    }
}
