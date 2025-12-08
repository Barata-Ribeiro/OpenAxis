<?php

namespace App\Http\Controllers\Management;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\UpdateVendorRequest;
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

        $allowedSorts = ['id', 'first_name', 'last_name', 'user_email', 'commission_rate', 'is_active', 'created_at', 'updated_at'];
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
            ->role(RoleEnum::VENDOR->value)
            ->whereNotIn('id', Vendor::pluck('user_id')->toArray())
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

    public function show(Vendor $vendor)
    {
        $vendor->load('user', 'user.roles', 'user.addresses', 'user.media');

        return Inertia::render('erp/vendors/show', [
            'vendor' => $vendor,
        ]);
    }

    public function edit(Vendor $vendor)
    {
        return Inertia::render('erp/vendors/edit', [
            'vendor' => $vendor,
        ]);
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $userId = Auth::id();

        try {
            Log::info('Vendor: Updating vendor.', ['action_user_id' => $userId, 'vendor_id' => $vendor->id]);

            $this->vendorService->updateVendor($request, $vendor);

            return to_route('erp.vendors.index')->with('success', "$vendor->full_name's vendor profile updated successfully.");
        } catch (Exception $e) {
            Log::error('Vendor: Error updating vendor.', [
                'action_user_id' => $userId,
                'vendor_id' => $vendor->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Error updating vendor.');
        }
    }

    public function destroy(Vendor $vendor)
    {
        $userId = Auth::id();

        try {
            Log::info('Vendor: Deleting vendor.', ['action_user_id' => $userId, 'vendor_id' => $vendor->id]);

            $vendor->delete();

            return to_route('erp.vendors.index')->with('success', "$vendor->full_name's vendor profile deleted successfully.");
        } catch (Exception $e) {
            Log::error('Vendor: Error deleting vendor.', [
                'action_user_id' => $userId,
                'vendor_id' => $vendor->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error deleting vendor.');
        }
    }

    public function forceDestroy(Vendor $vendor)
    {
        $userId = Auth::id();

        try {
            Log::info('Vendor: Force deleting vendor.', ['action_user_id' => $userId, 'vendor_id' => $vendor->id]);

            $vendor->forceDelete();

            return to_route('erp.vendors.index')->with('success', "$vendor->full_name's vendor profile permanently deleted successfully.");
        } catch (Exception $e) {
            Log::error('Vendor: Error force deleting vendor.', [
                'action_user_id' => $userId,
                'vendor_id' => $vendor->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error permanently deleting vendor.');
        }
    }
}
