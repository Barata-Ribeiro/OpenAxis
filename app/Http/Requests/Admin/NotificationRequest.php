<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleEnum;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{
    /**
     * Redirect back to the create page so Inertia can rehydrate props on validation errors.
     *
     * @var string
     */
    protected $redirectRoute = 'administrative.notifier.create';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:1200'],
            'email' => ['required_without:roles', 'email', 'exists:users,email', 'prohibits:roles'],
            'roles' => ['required_without:email', 'array', 'prohibits:email'],
            'roles.*' => ['string', Rule::in(array_map(fn (RoleEnum $r) => $r->value, RoleEnum::cases()))],
        ];
    }
}
