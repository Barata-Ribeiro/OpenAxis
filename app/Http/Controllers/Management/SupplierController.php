<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\SupplierRequest;
use App\Http\Requests\QueryRequest;
use App\Services\Management\SupplierService;
use Auth;
use Exception;
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

    public function create()
    {
        Log::info('Supplier: Accessed supplier creation page.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/suppliers/create');
    }

    public function store(SupplierRequest $request)
    {
        $userId = Auth::id();

        try {
            Log::info('Supplier: Store method called.', ['action_user_id' => $userId]);

            $this->supplierService->createSupplier($request);

            return to_route('erp.suppliers.index')->with('success', 'Supplier created successfully.');
        } catch (Exception $e) {
            Log::error('Supplier: Error occurred while storing supplier.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'An error occurred while creating the supplier. Please try again.');
        }
    }
}
