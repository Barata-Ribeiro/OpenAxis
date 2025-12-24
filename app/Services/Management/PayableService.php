<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Interfaces\Management\PayableServiceInterface;
use App\Models\Partner;
use App\Models\Payable;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PayableService implements PayableServiceInterface
{
    public function getPaginatedPayables(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $is_active = $filters['is_active'][0] ?? null;

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $sortByStartsWithSupplier = str_starts_with((string) $sortBy, 'supplier_name');

        if (! empty($sortBy) && $sortByStartsWithSupplier) {
            $sortBy = str_replace('supplier_name', 'partners.name', $sortBy);
        }

        return Payable::query()
            ->select('payables.id', 'payables.code', 'payables.amount', 'payables.due_date', 'payables.status', 'payables.supplier_id', 'payables.vendor_id', 'payables.created_at', 'payables.updated_at')
            ->with(['partner:id,name', 'vendor:id,name'])
            ->when($search, fn ($q, $search) => $q->whereLike('payables.code', "%$search%")
                ->orWhereLike('payables.description', "%$search%")->orWhereLike('payables.amount', "%$search%")
                ->orWhereHas('partner', fn ($partnerQuery) => $partnerQuery->whereLike('partners.name', "%$search%")->orWhereLike('partners.email', "%$search%"))
                ->orWhereHas('vendor', fn ($vendorQuery) => $vendorQuery->whereLike('vendors.name', "%$search%")->orWhereLike('vendors.email', "%$search%")))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($is_active, fn ($query) => $query->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->leftJoin(new Partner()->getTable(), 'payables.supplier_id', '=', 'partners.id')
            ->leftJoin(new Vendor()->getTable(), 'payables.vendor_id', '=', 'vendors.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
