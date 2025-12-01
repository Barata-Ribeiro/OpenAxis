<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Interfaces\Management\VendorServiceInterface;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorService implements VendorServiceInterface
{
    public function getPaginatedVendors(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $createdAtRange = $filters['created_at'] ?? [];

        [$start, $end] = Helpers::getDateRange($createdAtRange);

        return Vendor::query()
            ->with(['user', 'user.media'])
            ->when($search, fn ($q, $search) => $q->whereLike('first_name', "%$search%")
                ->orWhereLike('last_name', "%$search%")->orWhereLike('phone_number', "%$search%")
                ->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('name', "%$search%")->orWhereLike('email', "%$search%")))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
