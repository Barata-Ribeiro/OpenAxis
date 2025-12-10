<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Models\ProductCategory;
use App\Services\product\InventoryService;
use Inertia\Inertia;

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

        return Inertia::render('erp/inventory/index', [
            'inventory' => $inventory,
            'categories' => ProductCategory::pluck('name'),
        ]);
    }
}
