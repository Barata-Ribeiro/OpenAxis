<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp;

use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\PaymentMethod;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Exists;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class CashFlowEntryRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $kind = $this->route('kind');
        $rules = [
            'notes' => FluentRule::string()->nullable()->max(5_000),
        ];

        if ($kind === 'payment') {
            $rules['expected_amount_cents'] = FluentRule::integer()->required()->min(1);
        } else {
            $rules += [
                'description' => FluentRule::string()->required()->max(255),
                'amount_cents' => FluentRule::integer()->required()->between(1, 1_000_000_000_000),
            ];
        }

        if (in_array($kind, ['payable', 'expense'], true)) {
            $account = $this->route('financialAccount');
            $currentCategory = $account instanceof FinancialAccount ? $account->expense_category_id : null;
            $rules['expense_category_id'] = FluentRule::integer()->required()->exists(ExpenseCategory::class, 'id',
                fn (Exists $rule) => $rule->where(fn (Builder $query) => $query->whereNull('deleted_at')->orWhere('id', $currentCategory)));
        }

        if ($kind === 'payable') {
            return $rules + [
                'due_on' => FluentRule::date()->required()->format('Y-m-d'),
                'contact_name' => FluentRule::string()->nullable()->max(255),
                'contact_phone' => FluentRule::string()->nullable()->max(40),
            ];
        }

        return $rules + [
            'payment_method_id' => FluentRule::integer()->required()->exists(PaymentMethod::class, 'id',
                fn (Exists $rule) => $rule->withoutTrashed()->where('is_active', true)),
            'occurred_at' => FluentRule::date()->required()->format('Y-m-d\TH:i'),
        ];
    }
}
