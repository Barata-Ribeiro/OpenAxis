<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCategoryRequest;
use App\Http\Requests\Product\UpdateProductCategoryRequest;
use App\Http\Requests\QueryRequest;
use App\Models\ProductCategory;
use App\Services\Product\ProductCategoryService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

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

    public function create()
    {
        return Inertia::render('erp/product-categories/create');
    }

    public function store(ProductCategoryRequest $request)
    {
        $userId = Auth::id();

        try {
            Log::info('Product Category: Creation of new a new category.', ['action_user_id' => $userId]);

            $this->productCategoryService->createCategory($request);

            return to_route('erp.categories.index')->with('success', 'Product category created successfully.');
        } catch (Exception $e) {
            Log::error('Product Category: Failed to create new category.', [
                'action_user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to create product category.');
        }
    }

    public function edit(ProductCategory $category)
    {
        return Inertia::render('erp/product-categories/edit', [
            'category' => $category,
        ]);
    }

    public function update(ProductCategory $category, UpdateProductCategoryRequest $request)
    {
        $userId = Auth::id();

        try {
            Log::info('Product Category: Update category.', ['action_user_id' => $userId]);

            $this->productCategoryService->updateCategory($request, $category);

            return to_route('erp.categories.index')->with('success', 'Product category updated successfully.');
        } catch (Exception $e) {
            Log::error('Product Category: Failed to update category.', [
                'action_user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update product category.');
        }
    }
}
