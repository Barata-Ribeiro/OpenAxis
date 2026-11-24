<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp;

use App\Actions\Erp\TableQuery;
use Illuminate\Foundation\Http\FormRequest;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class DataTableRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        return [
            'table' => FluentRule::array(['page', 'perPage', 'search', 'filters', 'sorting']),
            'table.page' => FluentRule::integer()->between(1, 1_000_000),
            'table.perPage' => FluentRule::integer()->between(1, 100),
            'table.search' => FluentRule::string()->nullable()->max(200),
            'table.filters' => FluentRule::list()->max(20),
            'table.filters.*' => FluentRule::array(['id', 'operator', 'value', 'filterId', 'joinOperator']),
            'table.filters.*.id' => FluentRule::string()->required()->max(64),
            'table.filters.*.operator' => FluentRule::string()->required()->in(TableQuery::operators()),
            'table.filters.*.value' => FluentRule::field()->nullable(),
            'table.filters.*.filterId' => FluentRule::string()->max(64),
            'table.filters.*.joinOperator' => FluentRule::string()->in(['and', 'or']),
            'table.sorting' => FluentRule::list()->max(12),
            'table.sorting.*' => FluentRule::array(['id', 'desc']),
            'table.sorting.*.id' => FluentRule::string()->required()->distinct()->max(64),
            'table.sorting.*.desc' => FluentRule::boolean()->required(),
            'tab' => FluentRule::string()->in(['summary', 'receipts', 'expenses', 'payables']),
            'period' => FluentRule::string()->in(['today', 'week', 'month', 'custom']),
            'start' => FluentRule::date()->format('Y-m-d')->requiredIf('period', 'custom'),
            'end' => FluentRule::date()->format('Y-m-d')->requiredIf('period', 'custom')->afterOrEqual('start'),
        ];
    }

    public function tableQuery(): TableQuery
    {
        return new TableQuery($this->validated('table', []));
    }
}
