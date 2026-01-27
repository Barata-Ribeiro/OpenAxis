<?php

namespace App\Http\Requests\Management;

use App\Enums\ReceivableStatusEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReceivableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $route = $this->route();

        return match ($route->getName()) {
            'erp.receivables.store' => $user->hasPermissionTo('finance.create') || $user->hasRole('super-admin'),
            'erp.receivables.update' => $user->hasPermissionTo('finance.edit') || $user->hasRole('super-admin'),
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
            'client_id' => ['required', 'integer', 'exists:partners,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', Rule::in(array_map(fn (ReceivableStatusEnum $s) => $s->value, ReceivableStatusEnum::cases()))],
            'payment_method' => ['required', 'in:bank_transfer,cash,credit_card,check'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
