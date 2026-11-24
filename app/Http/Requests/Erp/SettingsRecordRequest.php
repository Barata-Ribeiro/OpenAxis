<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp;

use App\Actions\Erp\SettingsRecords;
use App\Concerns\ProfileValidationRules;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class SettingsRecordRequest extends FormRequest
{
    use HasFluentRules, ProfileValidationRules;

    protected function prepareForValidation(): void
    {
        if ($this->route('section') === 'users' && config('fortify.lowercase_usernames') && is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower($this->input('email'))]);
        }
    }

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $section = $this->route('section');
        abort_unless(is_string($section) && array_key_exists($section, SettingsRecords::MODULES), 404);
        $id = $this->route('record') === null ? null : (int) $this->route('record');
        $guard = new SettingsRecords($section)->guard();
        $name = $this->nameRules();
        $password = FluentRule::password()->min(8)->confirmed()->max(255);

        return match ($section) {
            'payment-methods' => [
                'name' => $name->unique(PaymentMethod::class, 'name', fn (Unique $rule) => $rule->ignore($id)),
                'fee_type' => FluentRule::string()->required()->in([PaymentMethod::FEE_FIXED, PaymentMethod::FEE_PERCENTAGE]),
                'fee_rate' => FluentRule::integer()->required()->between(0, $this->input('fee_type') === PaymentMethod::FEE_PERCENTAGE ? 10_000 : 4_294_967_295),
                'is_active' => FluentRule::boolean()->required(),
            ],
            'expense-categories' => [
                'name' => $name->unique(ExpenseCategory::class, 'name', fn (Unique $rule) => $rule->ignore($id)),
            ],
            'categories' => [
                'name' => $name,
                'parent_id' => FluentRule::integer()->nullable()->exists(Category::class, 'id', fn (Exists $rule) => $rule->withoutTrashed()),
            ],
            'units' => [
                'name' => $name,
                'code' => $id === null ? FluentRule::string()->required()->max(16)->unique(UnitOfMeasure::class) : FluentRule::field()->prohibited(),
                'quantity_decimals' => $id === null ? FluentRule::integer()->required()->between(0, 3) : FluentRule::field()->prohibited(),
            ],
            'locations' => [
                'name' => $name,
                'code' => FluentRule::string()->required()->max(48)->unique(StorageLocation::class, 'code', fn (Unique $rule) => $rule->ignore($id)),
                'is_active' => FluentRule::boolean()->required(),
            ],
            'users' => $this->profileRules($id) + [
                'password' => $id === null ? $password->required() : $password->nullable(),
                'role_ids' => FluentRule::list()->max(50)->each(
                    FluentRule::integer()->required()->distinct()->exists(Role::class, 'id', fn (Exists $rule) => $rule->where('guard_name', $guard)),
                ),
            ],
            'roles' => [
                'name' => $name->unique(Role::class, 'name', fn (Unique $rule) => $rule->where('guard_name', $guard)->ignore($id)),
                'permission_ids' => FluentRule::list()->max(200)->each(
                    FluentRule::integer()->required()->distinct()->exists(Permission::class, 'id', fn (Exists $rule) => $rule->where('guard_name', $guard)),
                ),
            ],
        };
    }
}
