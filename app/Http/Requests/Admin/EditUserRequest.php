<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('user.edit') || $user->hasRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:320', Rule::unique(User::class)->ignore($this->route('user')->id ?? $this->route('user'))],
            'password' => ['sometimes', 'nullable', Password::defaults(), 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
