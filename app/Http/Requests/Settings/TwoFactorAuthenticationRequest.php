<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\InteractsWithTwoFactorState;
use SanderMuller\FluentValidation\HasFluentRules;

class TwoFactorAuthenticationRequest extends FormRequest
{
    use HasFluentRules, InteractsWithTwoFactorState;
}
