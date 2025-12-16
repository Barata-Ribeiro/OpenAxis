<?php

namespace App\Http\Requests\Admin;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $route = $this->route();

        return match ($route->getName()) {
            'administrative.roles.store' => $user->hasPermissionTo('role.create') || $user->hasRole('super-admin'),
            'administrative.roles.update' => $user->hasPermissionTo('role.edit') || $user->hasRole('super-admin'),
            default => false,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Role::class)->ignore($this->route('role')?->id)],
            'permissions' => ['array', 'min:1'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
