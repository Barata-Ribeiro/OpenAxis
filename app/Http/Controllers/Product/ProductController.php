<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Models\ProductCategory;
use App\Services\Product\ProductService;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'sku', 'name', 'category_name', 'cost_price', 'selling_price', 'current_stock',  'comission', 'is_active', 'created_at', 'updated_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $products = $this->productService->getPaginatedProducts(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('erp/products/index', [
            'products' => $products,
            'categories' => ProductCategory::pluck('name'),
        ]);
    }
}
