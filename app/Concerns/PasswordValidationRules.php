<?php

namespace App\Concerns;

use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\Rules\PasswordRule;
use SanderMuller\FluentValidation\Rules\StringRule;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     */
    protected function passwordRules(): PasswordRule
    {
        return FluentRule::password()->required()->confirmed();
    }

    /**
     * Get the validation rules used to validate the current password.
     */
    protected function currentPasswordRules(): StringRule
    {
        return FluentRule::string()->required()->currentPassword();
    }
}
