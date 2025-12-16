<?php

namespace App\Http\Requests\Management;

use App\Rules\IsValidIdentification;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $route = $this->route();

        return match ($route->getName()) {
            'erp.suppliers.store' => $user->hasPermissionTo('supplier.create') || $user->hasRole('super-admin'),
            'erp.suppliers.update' => $user->hasPermissionTo('supplier.edit') || $user->hasRole('super-admin'),
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:320', 'unique:users,email'],
            'identification' => ['required', 'string', 'max:50', new IsValidIdentification],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?(\d{1,3})?[-.\s]?(\(?\d{3}\)?[-.\s]?)?(\d[-.\s]?){6,9}\d$/'],
            'supplier_type' => ['required', 'string', 'in:supplier,both'],

            'type' => ['required', 'in:billing,shipping,billing_and_shipping,other'],
            'label' => ['nullable', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:150'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['required', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('is_primary')) {
            $isActive = $this->input('is_primary');

            $this->merge([
                'is_primary' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
            ]);
        } else {
            $this->merge([
                'is_primary' => false,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'supplier_type.in' => 'The selected supplier type is invalid.',
        ];
    }
}
