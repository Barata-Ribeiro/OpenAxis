<?php

namespace App\Http\Requests\Management;

use App\Enums\PayableStatusEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $route = $this->route();

        return match ($route->getName()) {
            'erp.payables.store' => $user->hasPermissionTo('finance.create') || $user->hasRole('super-admin'),
            'erp.payables.update' => $user->hasPermissionTo('finance.edit') || $user->hasRole('super-admin'),
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
            'description' => ['required', 'string', 'max:255'],
            'supplier_id' => ['required', 'integer', 'exists:partners,id'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', Rule::in(array_map(fn (PayableStatusEnum $s) => $s->value, PayableStatusEnum::cases()))],
            'payment_method' => ['required', 'in:bank_transfer,cash,credit_card,check'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
