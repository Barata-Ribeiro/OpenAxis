<?php

namespace Database\Factories;

use App\Models\CashEntry;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CashEntry> */
class CashEntryFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'type' => CashEntry::TYPE_RECEIPT,
            'payment_method_id' => PaymentMethod::factory(),
            'description' => fake()->sentence(),
            'amount_cents' => 1_000,
            'occurred_at' => now(),
        ];
    }

    public function expense(?ExpenseCategory $category = null): static
    {
        return $this->state(fn (): array => [
            'type' => CashEntry::TYPE_EXPENSE,
            'expense_category_id' => $category?->getKey() ?? ExpenseCategory::factory(),
        ]);
    }

    public function forAccount(FinancialAccount $account): static
    {
        return $this->for($account, 'financialAccount')->state(fn (): array => [
            'type' => $account->type === FinancialAccount::TYPE_PAYABLE
                ? CashEntry::TYPE_EXPENSE
                : CashEntry::TYPE_RECEIPT,
            'expense_category_id' => null,
        ]);
    }
}
