<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Product\ProductService;
use Auth;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Log;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(QueryRequest $request)
    {
        $products = $this->getPaginatedProductsFromRequest($request);

        return Inertia::render('erp/products/index', [
            'products' => $products,
            'categories' => ProductCategory::pluck('name'),
        ]);
    }

    public function create()
    {
        return Inertia::render('erp/products/create', [
            'categories' => ProductCategory::pluck('name'),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $userId = Auth::id();
        try {
            Log::info('Product: Creation of new product.', ['action_user_id' => $userId]);

            $this->productService->createProduct($request);

            return to_route('erp.products.index')->with('success', 'Product created successfully.');
        } catch (Exception $e) {
            Log::error('Product: Failed to create product.', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->withInput()->with(['error' => 'Failed to create product.']);
        }
    }

    public function show(Product $product)
    {
        Log::info('Product: Viewing product details.', ['product_id' => $product->id, 'action_user_id' => Auth::id()]);

        return Inertia::render('erp/products/show', [
            'product' => $product->load('category:id,name'),
        ]);
    }

    public function edit(Product $product)
    {
        Log::info('Product: Editing product form.', ['product_id' => $product->id, 'action_user_id' => Auth::id()]);

        return Inertia::render('erp/products/edit', [
            'product' => $product->load('category:id,name')->makeVisible('media'),
            'categories' => ProductCategory::pluck('name'),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $userId = Auth::id();
        try {
            Log::info('Product: Updating product.', ['product_id' => $product->id, 'action_user_id' => $userId]);

            $this->productService->updateProduct($request, $product);

            return to_route('erp.products.index')->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            Log::error('Product: Failed to update product.', ['product_id' => $product->id, 'action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->withInput()->with(['error' => 'Failed to update product.']);
        }
    }

    public function destroy(Product $product)
    {
        $userId = Auth::id();
        try {
            Log::info('Product: Deleting product.', ['product_id' => $product->id, 'action_user_id' => $userId]);

            $product->deleteOrFail();

            return to_route('erp.products.index')->with('success', 'Product deleted successfully.');
        } catch (Exception $e) {
            Log::error('Product: Failed to delete product.', ['product_id' => $product->id, 'action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->with(['error' => 'Failed to delete product.']);
        }
    }

    public function forceDestroy($productRouteKey)
    {
        $userId = Auth::id();
        try {

            $routeKeyName = (new Product)->getRouteKeyName();

            $product = Product::withTrashed()->where($routeKeyName, $productRouteKey)->firstOrFail();

            $this->productService->forceDeleteProduct($product);

            Log::info('Product: Permanently deleting product.', ['product_id' => $product->id, 'action_user_id' => $userId]);

            return to_route('erp.products.index')->with('success', 'Product permanently deleted successfully.');
        } catch (Exception $e) {
            Log::error('Product: Failed to permanently delete product.', ['product_id' => $product->id, 'action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->with(['error' => 'Failed to permanently delete product.']);
        }
    }

    public function generateCsv(QueryRequest $request)
    {
        $userId = Auth::id();
        try {
            $products = $this->getPaginatedProductsFromRequest($request);

            if ($products->isEmpty()) {
                return back()->with(['error' => 'No products found for the given criteria.']);
            }

            return $this->productService->generateCsvExport($products);
        } catch (Exception $e) {
            Log::error('Product: Failed to generate CSV export.', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->with('error', 'An unknown error occurred while generating the CSV export.');
        }
    }

    /**
     * Build and return a LengthAwarePaginator of products based on the given request.
     *
     * Applies filtering, searching, sorting and eager-loading options provided by the
     * validated QueryRequest, then paginates the resulting query.
     *
     * Expected request inputs (handled/validated by QueryRequest):
     *  - page / per_page: pagination parameters
     *  - sort: sorting column/direction
     *  - filters: associative array of field => value
     *  - with: relations to eager-load
     *
     * @param  QueryRequest  $request  Validated query parameters for filtering, sorting and pagination.
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of User models.
     */
    private function getPaginatedProductsFromRequest(QueryRequest $request): LengthAwarePaginator
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'sku', 'name', 'category_name', 'cost_price', 'selling_price', 'current_stock',  'comission', 'is_active', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        return $this->productService->getPaginatedProducts(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );
    }
}
