<?php

namespace App\Http\Requests\Settings;

use App\Concerns\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\HasFluentRules;

class ProfileDeleteRequest extends FormRequest
{
    use HasFluentRules, PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, FluentRuleContract>
     */
    public function rules(): array
    {
        return [
            'password' => $this->currentPasswordRules(),
        ];
    }
}
