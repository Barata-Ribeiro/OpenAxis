<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\Management\UpdateVendorRequest;
use App\Http\Requests\Management\VendorRequest;
use App\Interfaces\Management\VendorServiceInterface;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorService implements VendorServiceInterface
{
    public function getPaginatedVendors(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $createdAtRange = $filters['created_at'] ?? [];
        $is_active = $filters['is_active'][0] ?? null;

        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $query = Vendor::query()
            ->with([
                'user' => fn ($q) => $q->select('id', 'name', 'email'),
                'user.media',
            ])
            ->when($search, fn ($q, $search) => $q->whereLike('first_name', "%$search%")
                ->orWhereLike('last_name', "%$search%")->orWhereLike('phone_number', "%$search%")
                ->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('name', "%$search%")->orWhereLike('email', "%$search%")))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($is_active, fn ($query) => $query->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->withTrashed();

        if (str_starts_with($sortBy, 'user.')) {
            $relatedField = str_replace('user.', '', $sortBy);
            $query->join('users', 'vendors.user_id', '=', 'users.id')
                ->orderBy("users.{$relatedField}", $sortDir)
                ->select('vendors.*');
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->paginate($perPage)->withQueryString();
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
