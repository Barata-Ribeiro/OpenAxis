<?php

namespace App\Http\Requests\Management;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class SaleOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('sale.create') || $user->hasRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:partners,id'],
            'vendor_id' => ['required', 'exists:vendors,id'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'payment_condition_id' => ['nullable', 'exists:payment_conditions,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal_price' => ['required', 'numeric', 'min:0'],
            'items.*.commission_item' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,delivered,canceled'],
            'payment_method' => ['required', 'in:cash,credit_card,debit_card,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
