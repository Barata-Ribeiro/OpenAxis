<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Settings;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class PaymentMethodRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        return [
            'name' => FluentRule::string()->required()->max(255)->unique(PaymentMethod::class, 'name', fn (Unique $rule) => $rule->ignore($this->route('paymentMethod'))),
            'fee_type' => FluentRule::string()->required()->in([PaymentMethod::FEE_FIXED, PaymentMethod::FEE_PERCENTAGE]),
            'fee_rate' => FluentRule::integer()->required()->between(0, $this->input('fee_type') === PaymentMethod::FEE_PERCENTAGE ? 10_000 : 4_294_967_295),
            'is_active' => FluentRule::boolean()->required(),
        ];
    }
}
