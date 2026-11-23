<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Validation\Rules\Unique;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\Rules\EmailRule;
use SanderMuller\FluentValidation\Rules\StringRule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, FluentRuleContract>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     */
    protected function nameRules(): StringRule
    {
        return FluentRule::string()->required()->max(255);
    }

    /**
     * Get the validation rules used to validate user emails.
     */
    protected function emailRules(?int $userId = null): EmailRule
    {
        return FluentRule::email(defaults: false)
            ->required()
            ->max(255)
            ->unique(User::class, 'email', fn (Unique $rule): Unique => $rule->ignore($userId));
    }
}
