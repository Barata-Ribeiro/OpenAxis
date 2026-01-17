<?php

namespace App\Http\Requests\Management;

use App\Enums\RoleEnum;
use App\Models\Client;
use App\Rules\IsValidIdentification;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('client.edit') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
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
            'email' => ['required', 'string', 'email', 'max:320', Rule::unique(Client::class)->ignore($this->route('client')->id ?? $this->route('client'))],
            'identification' => ['required', 'string', 'max:50', new IsValidIdentification],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?(\d{1,3})?[-.\s]?(\(?\d{3}\)?[-.\s]?)?(\d[-.\s]?){6,9}\d$/'],
            'client_type' => ['required', 'string', 'in:individual,company'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_type.in' => 'The selected client type is invalid.',
        ];
    }
}
