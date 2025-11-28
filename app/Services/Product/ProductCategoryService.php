<?php

namespace App\Services\Product;

use App\Http\Requests\Product\ProductCategoryRequest;
use App\Http\Requests\Product\UpdateProductCategoryRequest;
use App\Interfaces\Product\ProductCategoryServiceInterface;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductCategoryService implements ProductCategoryServiceInterface
{
    public function getPaginatedCategories(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $is_active = $filters['is_active'][0] ?? null;

        return ProductCategory::query()
            ->withCount('products')
            ->when($search, fn ($query) => $query->whereLike('name', "%$search%")->orWhereLike('description', "%$search%"))
            ->when($is_active, fn ($query) => $query->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createCategory(ProductCategoryRequest $request): void
    {
        $validated = $request->validated();

        ProductCategory::create($validated);
    }

    public function updateCategory(UpdateProductCategoryRequest $request, ProductCategory $category): void
    {
        $validated = $request->validated();

        $category->update($validated);
    }
}
