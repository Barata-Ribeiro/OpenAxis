<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Interfaces\Management\SupplierServiceInterface;
use App\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService implements SupplierServiceInterface
{
    public function getPaginatedSuppliers(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $is_active = $filters['is_active'][0] ?? null;

        return Partner::query()
            ->whereType('supplier')
            ->when($search, fn ($query, $search) => $query->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%")->orWhereLike('phone_number', "%$search%")
                ->orWhereLike('identification', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($is_active, fn ($q) => $q->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
