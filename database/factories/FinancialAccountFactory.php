<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FinancialAccount> */
class FinancialAccountFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'type' => FinancialAccount::TYPE_RECEIVABLE,
            'description' => fake()->sentence(),
            'amount_cents' => 10_000,
            'due_on' => today()->addDays(30),
        ];
    }

    public function payable(?ExpenseCategory $category = null): static
    {
        return $this->state(fn (): array => [
            'type' => FinancialAccount::TYPE_PAYABLE,
            'expense_category_id' => $category?->getKey() ?? ExpenseCategory::factory(),
        ]);
    }
}
