<?php

namespace App\Http\Requests\Management;

use App\Enums\PurchaseOrderStatusEnum;
use App\Enums\RoleEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('order.edit') || $user->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['sometimes', 'exists:partners,id'],
            'order_date' => ['sometimes', 'date', 'before_or_equal:forecast_date'],
            'forecast_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:order_date'],
            'status' => ['sometimes', Rule::in(array_map(fn (PurchaseOrderStatusEnum $s) => $s->value, PurchaseOrderStatusEnum::cases()))],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
