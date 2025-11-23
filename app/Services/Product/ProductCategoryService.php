<?php

namespace App\Services\Product;

use App\Common\Helpers;
use App\Interfaces\Product\ProductCategoryServiceInterface;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductCategoryService implements ProductCategoryServiceInterface
{
    public function getPaginatedCategories(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $is_active = Helpers::buildFiltersArray($filters, 'is_active');

        return ProductCategory::query()
            ->withCount('products')
            ->when($search, fn ($query) => $query->whereLike('name', "%$search%")->orWhereLike('description', "%$search%"))
            ->when($is_active, fn ($query) => $query->where('is_active', filter_var($is_active[0], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
