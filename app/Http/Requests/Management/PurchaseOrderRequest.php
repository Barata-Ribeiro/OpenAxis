<?php

namespace App\Http\Requests\Management;

use App\Enums\PurchaseOrderStatusEnum;
use App\Enums\RoleEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('order.create') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:partners,id'],
            'order_date' => ['required', 'date', 'before_or_equal:forecast_date'],
            'forecast_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(array_map(fn (PurchaseOrderStatusEnum $s) => $s->value, PurchaseOrderStatusEnum::cases()))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
