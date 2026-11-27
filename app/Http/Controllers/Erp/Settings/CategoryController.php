<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class CategoryController extends Controller
{
    use ErpResourceResponses;

    private const string PAGE = 'erp/settings/categories';

    private const string INDEX_ROUTE = 'erp.settings.categories.index';

    private const string INDEX_PERMISSION = 'category.index';

    public function index(DataTableRequest $request): Response
    {
        $query = Category::query();
        $archived = $request->validated('trashed') === 'only';

        if ($archived) {
            $query->onlyTrashed();
        }

        return $this->renderResourceIndex(
            $request, self::PAGE, $query->toBase(),
            ['name' => 'text'], archived: $archived,
        );
    }

    public function create(Request $request): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/create', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'New product category',
            'values' => [],
            'choices' => ['parent_id' => $this->parentOptions()],
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => Category::query()->create($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Product category created.'));
    }

    public function show(Request $request, Category $category): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $category->name,
            'details' => [
                'Name' => $category->name,
                'Archive status' => $category->trashed() ? 'Archived' : 'Current',
                'Parent category' => $category->parent()->value('name'),
            ],
        ]);
    }

    public function edit(Request $request, Category $category): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/edit', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Edit product category',
            'id' => $category->getKey(),
            'values' => $category->only(['name', 'parent_id']),
            'choices' => ['parent_id' => $this->parentOptions($category)],
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->writeResource(fn () => Category::query()->whereKey($category->getKey())->lockForUpdate()->firstOrFail()->update($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Product category updated.'));
    }

    public function archive(Request $request, Category $category): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Archive '.$category->name,
            'id' => $category->getKey(),
        ]);
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->writeResource(fn () => Category::query()->whereKey($category->getKey())->lockForUpdate()->firstOrFail()->delete());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Product category archived.'));
    }

    public function restore(Request $request, Category $category): RedirectResponse
    {
        $this->writeResource(fn () => Category::onlyTrashed()->whereKey($category->getKey())->lockForUpdate()->firstOrFail()->restore());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Product category restored.'));
    }

    /** @return Collection<int, Category> */
    private function parentOptions(?Category $category = null): Collection
    {
        return Category::query()
            ->when($category, fn ($query) => $query->whereKeyNot($category->getKey()))
            ->orderBy('name')->get(['id', 'name']);
    }
}
