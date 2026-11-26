<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Exists;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;
use Spatie\Permission\Models\Role;

final class UserRequest extends FormRequest
{
    use HasFluentRules, ProfileValidationRules;

    protected function prepareForValidation(): void
    {
        if (config('fortify.lowercase_usernames') && is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower($this->input('email'))]);
        }
    }

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $user = $this->route('user');
        $editing = $user instanceof User;
        $password = FluentRule::password()->min(8)->confirmed()->max(255);
        $guard = config('auth.defaults.guard', 'web');

        return $this->profileRules($editing ? $user->id : null) + [
            'password' => $editing ? $password->nullable() : $password->required(),
            'role_ids' => FluentRule::list()->max(50)->each(
                FluentRule::integer()->required()->distinct()->exists(Role::class, 'id', fn (Exists $rule) => $rule->where('guard_name', $guard)),
            ),
        ];
    }
}
