<?php

namespace App\Http\Controllers\Management;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\VendorRequest;
use App\Http\Requests\QueryRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Management\VendorService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class VendorController extends Controller
{
    public function __construct(private VendorService $vendorService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'first_name', 'last_name', 'user.email', 'commission_rate', 'is_active', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $vendors = $this->vendorService->getPaginatedVendors(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('erp/vendors/index', [
            'vendors' => $vendors,
        ]);
    }

    public function create()
    {
        $usersWithVendorRole = User::select(['id', 'name'])
            ->whereHas('roles', fn ($q) => $q->where('name', RoleEnum::VENDOR->value))
            ->whereNotIn('id', Vendor::pluck('user_id'))
            ->get();

        return Inertia::render('erp/vendors/create', [
            'users' => Inertia::defer(fn () => $usersWithVendorRole),
        ]);
    }

    public function store(VendorRequest $request)
    {
        $userId = Auth::id();

        try {
            Log::info('Vendor: Creation of a new vendor.', ['action_user_id' => $userId]);

            $vendor = $this->vendorService->createVendor($request);

            return to_route('erp.vendors.index')->with('success', "$vendor->full_name's vendor profile created successfully.");
        } catch (Exception $e) {
            Log::error('Vendor: Error creating vendor.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Error creating vendor.');
        }
    }
}
