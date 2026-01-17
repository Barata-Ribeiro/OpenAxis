<?php

namespace App\Http\Requests\Management;

use App\Enums\RoleEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('vendor.edit') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'date_of_birth' => ['sometimes', 'date'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?(\d{1,3})?[-.\s]?(\(?\d{3}\)?[-.\s]?)?(\d[-.\s]?){6,9}\d$/'],
            'commission_rate' => ['required', 'numeric', 'between:0,100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('is_active')) {
            $isActive = $this->input('is_active');

            $this->merge([
                'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
            ]);
        } else {
            $this->merge([
                'is_active' => false,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'is_active.boolean' => 'The activation status must be true or false.',
        ];
    }
}
