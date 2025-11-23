<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Product\ProductCategoryService;
use Inertia\Inertia;

class ProductCategoryController extends Controller
{
    public function __construct(private ProductCategoryService $productCategoryService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'name', 'is_active', 'created_at', 'updated_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $categories = $this->productCategoryService->getPaginatedCategories(
            search: $search,
            filters: $filters,
            sortBy: $sortBy,
            sortDir: $sortDir,
            perPage: $perPage,
        );

        return Inertia::render('erp/product-categories/index', [
            'categories' => $categories,
        ]);
    }
}
