<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\InteractsWithTwoFactorState;
use SanderMuller\FluentValidation\HasFluentRules;

final class TwoFactorAuthenticationRequest extends FormRequest
{
    use HasFluentRules, InteractsWithTwoFactorState;
}
