<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class RoleRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $guard = config('auth.defaults.guard', 'web');

        return [
            'name' => FluentRule::string()->required()->max(255)->unique(
                Role::class, 'name', fn (Unique $rule) => $rule->where('guard_name', $guard)->ignore($this->route('role')),
            ),
            'permission_ids' => FluentRule::list()->max(200)->each(
                FluentRule::integer()->required()->distinct()->exists(Permission::class, 'id', fn (Exists $rule) => $rule->where('guard_name', $guard)),
            ),
        ];
    }
}
