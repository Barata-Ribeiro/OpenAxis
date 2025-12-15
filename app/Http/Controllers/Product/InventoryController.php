<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AdjustInventoryRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Product\InventoryService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'sku', 'name', 'category_name', 'current_stock', 'minimum_stock', 'comission', 'selling_price', 'is_active'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $inventory = $this->inventoryService->getPaginatedInventory(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        Log::info('Inventory: Viewed inventory list.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/inventory/index', [
            'inventory' => $inventory,
            'categories' => ProductCategory::pluck('name'),
        ]);
    }

    public function show(QueryRequest $request, Product $product)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'movement_type', 'quantity', 'created_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $movements = $this->inventoryService->getPaginatedStockMovements(
            $product->id,
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        Log::info('Inventory: Viewing product inventory.', ['action_user_id' => Auth::id(), 'product_id' => $product->id]);

        return Inertia::render('erp/inventory/show', [
            'product' => $product->only(['id', 'name', 'slug', 'current_stock', 'minimum_stock']),
            'movements' => $movements,
        ]);
    }

    public function create(QueryRequest $request)
    {
        Log::info('Inventory: Accessed inventory adjustment creation page.', ['action_user_id' => Auth::id()]);

        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        $products = $this->inventoryService->getProductsForSelect($search);

        return Inertia::render('erp/inventory/create', [
            'products' => Inertia::scroll(fn () => $products),
        ]);
    }

    public function edit(Product $product)
    {
        Log::info('Inventory: Editing product inventory.', ['action_user_id' => Auth::id(), 'product_id' => $product->id]);

        return Inertia::render('erp/inventory/edit', [
            'product' => $product->only(['id', 'name', 'slug', 'current_stock', 'minimum_stock']),
        ]);
    }

    public function update(AdjustInventoryRequest $request, Product $product)
    {
        $userId = Auth::id();
        Log::info('Inventory: Updating product inventory.', ['action_user_id' => $userId, 'product_id' => $product->id]);

        try {
            $this->inventoryService->adjustInventory($request, $product);

            Log::info('Inventory: Successfully updated product inventory.', ['action_user_id' => $userId, 'product_id' => $product->id]);

            return to_route('erp.inventory.index')->with('success', 'Inventory adjusted successfully.');
        } catch (Exception $e) {
            Log::error('Inventory: Failed to update product inventory.', [
                'action_user_id' => $userId,
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to adjust inventory. Try again later.');
        }
    }
}
