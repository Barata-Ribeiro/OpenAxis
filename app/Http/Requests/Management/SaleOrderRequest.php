<?php

namespace App\Http\Requests\Management;

use App\Enums\RoleEnum;
use App\Models\Product;
use Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SaleOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('sale.create') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
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
            'update_payables' => ['sometimes', 'boolean'],
            'update_receivables' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'update_payables' => filter_var($this->input('update_payables', false), FILTER_VALIDATE_BOOLEAN),
            'update_receivables' => filter_var($this->input('update_receivables', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Add after-validation hook to ensure requested quantities do not exceed stock.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? null;

                if (empty($productId) || $quantity === null) {
                    continue;
                }

                $insufficient = Product::whereId($productId)
                    ->where('current_stock', '<', $quantity)
                    ->exists();

                if ($insufficient) {
                    $validator->errors()->add(
                        "items.$index.quantity",
                        'Requested quantity exceeds current stock.'
                    );
                }
            }
        });
    }
}
