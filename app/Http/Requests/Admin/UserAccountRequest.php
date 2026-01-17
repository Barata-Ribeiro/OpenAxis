<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('user.create') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to this request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:320', 'unique:users,email'],
            'password' => ['sometimes', 'nullable', Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('password')) {
            $this->merge(['password' => Str::password()]);
        }
    }
}
