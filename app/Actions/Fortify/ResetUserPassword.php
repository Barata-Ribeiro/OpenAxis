<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use SanderMuller\FluentValidation\RuleSet;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        RuleSet::from([
            'password' => $this->passwordRules(),
        ])->validate($input);

        $user->forceFill([
            'password' => $input['password'],
        ])->save();
    }
}
