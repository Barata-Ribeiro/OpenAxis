<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Management\SupplierService;
use Auth;
use Inertia\Inertia;
use Log;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $supplierService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'name', 'email', 'identification', 'is_active', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $suppliers = $this->supplierService->getPaginatedSuppliers(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        Log::info('Supplier: Accessed supplier list.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/suppliers/index', [
            'suppliers' => $suppliers,
        ]);
    }
}
