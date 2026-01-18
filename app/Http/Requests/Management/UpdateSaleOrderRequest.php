<?php

namespace App\Http\Requests\Management;

use App\Enums\RoleEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('sale.edit') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'exists:partners,id'],
            'vendor_id' => ['sometimes', 'exists:vendors,id'],
            'payment_condition_id' => ['sometimes', 'nullable', 'exists:payment_conditions,id'],
            'delivery_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:order_date'],
            'status' => ['sometimes', 'in:pending,delivered,canceled'],
            'discount_cost' => ['sometimes', 'numeric', 'min:0'],
            'delivery_cost' => ['sometimes', 'numeric', 'min:0'],
            'payment_method' => ['sometimes', 'in:cash,credit_card,debit_card,bank_transfer'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
