<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\ExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class ExpenseCategoryController extends Controller
{
    use ErpResourceResponses;

    private const string PAGE = 'erp/settings/expense-categories';

    private const string INDEX_ROUTE = 'erp.settings.expense-categories.index';

    private const string INDEX_PERMISSION = 'expense-category.index';

    public function index(DataTableRequest $request): Response
    {
        $query = ExpenseCategory::query();
        $archived = $request->validated('trashed') === 'only';

        if ($archived) {
            $query->onlyTrashed();
        }

        return $this->renderResourceIndex(
            $request, self::PAGE, $query->toBase(),
            ['name' => 'text', 'is_system' => 'number'], ['is_system'], archived: $archived,
        );
    }

    public function create(Request $request): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/create', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'New expense category',
            'values' => [],
        ]);
    }

    public function store(ExpenseCategoryRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => ExpenseCategory::query()->create($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Expense category created.'));
    }

    public function show(Request $request, ExpenseCategory $expenseCategory): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $expenseCategory->name,
            'details' => [
                'Name' => $expenseCategory->name,
                'Archive status' => $expenseCategory->trashed() ? 'Archived' : 'Current',
                'System category' => $expenseCategory->is_system ? 'Yes' : 'No',
            ],
        ]);
    }

    public function edit(Request $request, ExpenseCategory $expenseCategory): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/edit', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Edit expense category',
            'id' => $expenseCategory->getKey(),
            'values' => $expenseCategory->only(['name']),
        ]);
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $this->writeResource(fn () => ExpenseCategory::query()->whereKey($expenseCategory->getKey())->lockForUpdate()->firstOrFail()->update($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Expense category updated.'));
    }

    public function archive(Request $request, ExpenseCategory $expenseCategory): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Archive '.$expenseCategory->name,
            'id' => $expenseCategory->getKey(),
        ]);
    }

    public function destroy(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $this->writeResource(function () use ($expenseCategory): void {
            $locked = ExpenseCategory::query()->whereKey($expenseCategory->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->is_system) {
                throw new DomainException('This record is still in use and cannot be removed.');
            }

            $locked->delete();
        });

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Expense category archived.'));
    }

    public function restore(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $this->writeResource(fn () => ExpenseCategory::onlyTrashed()->whereKey($expenseCategory->getKey())->lockForUpdate()->firstOrFail()->restore());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Expense category restored.'));
    }
}
