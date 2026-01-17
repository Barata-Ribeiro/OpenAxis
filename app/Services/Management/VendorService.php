<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\Management\UpdateVendorRequest;
use App\Http\Requests\Management\VendorRequest;
use App\Interfaces\Management\VendorServiceInterface;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorService implements VendorServiceInterface
{
    public function getPaginatedVendors(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $is_active = $filters['is_active'][0] ?? null;
        $vendorUserEmail = $filters['user_email'] ?? [];

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $sortByStartsWithUser = str_starts_with((string) $sortBy, 'user_email');

        if (! empty($sortBy) && $sortByStartsWithUser) {
            $sortBy = str_replace('user_email', 'users.email', $sortBy);
        }

        return Vendor::query()
            ->select('vendors.*')
            ->with(['user:id,name,email', 'user.media'])
            ->when($search, fn ($q, $search) => $q->whereLike('vendors.first_name', "%$search%")
                ->orWhereLike('vendors.last_name', "%$search%")->orWhereLike('vendors.phone_number', "%$search%")
                ->orWhereRaw("CONCAT(vendors.first_name, ' ', vendors.last_name) LIKE ?", ["%$search%"])
                ->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('users.name', "%$search%")->orWhereLike('users.email', "%$search%")))
            ->when($vendorUserEmail, fn ($q) => $q->whereHas('user', fn ($userQuery) => $userQuery->whereIn('users.email', $vendorUserEmail)))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($is_active, fn ($query) => $query->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->leftJoin((new User)->getTable(), 'vendors.user_id', '=', 'users.id')
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createVendor(VendorRequest $request): Vendor
    {
        $validated = $request->validated();

        return Vendor::create($validated);
    }

    public function updateVendor(UpdateVendorRequest $request, Vendor $vendor): void
    {
        $validated = $request->validated();

        $vendor->update($validated);
    }
}
