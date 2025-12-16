<?php

namespace App\Http\Requests\Management;

use App\Models\PaymentCondition;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentConditionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $route = $this->route();

        return match ($route->getName()) {
            'erp.payment-conditions.store' => $user->hasPermissionTo('finance.create') || $user->hasRole('super-admin'),
            'erp.payment-conditions.update' => $user->hasPermissionTo('finance.edit') || $user->hasRole('super-admin'),
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
            'code' => ['required', 'string', 'max:20', Rule::unique(PaymentCondition::class)->ignore($this->route('paymentCondition')?->id)],
            'name' => ['required', 'string', 'max:100'],
            'days_until_due' => ['required', 'integer', 'min:0'],
            'installments' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
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
}
