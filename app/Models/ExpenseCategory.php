<?php

namespace App\Models;

use Database\Factories\ExpenseCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['name'])]
class ExpenseCategory extends Model implements Auditable
{
    /** @use HasFactory<ExpenseCategoryFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    /** @return HasMany<FinancialAccount, $this> */
    public function financialAccounts(): HasMany
    {
        return $this->hasMany(FinancialAccount::class);
    }

    /** @return HasMany<CashEntry, $this> */
    public function cashEntries(): HasMany
    {
        return $this->hasMany(CashEntry::class);
    }
}
