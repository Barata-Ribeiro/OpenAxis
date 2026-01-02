<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\Management\PayableRequest;
use App\Http\Requests\QueryRequest;
use App\Interfaces\Management\PayableServiceInterface;
use App\Models\Partner;
use App\Models\Payable;
use App\Models\Vendor;
use Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Str;

class PayableService implements PayableServiceInterface
{
    public function getPaginatedPayables(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $status = $filters['status'] ?? null;

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $dueDateRange = $filters['due_date'] ?? [];
        [$dueStart, $dueEnd] = Helpers::getDateRange($dueDateRange);

        $sortByStartsWithSupplier = str_starts_with((string) $sortBy, 'supplier_name');

        if (! empty($sortBy) && $sortByStartsWithSupplier) {
            $sortBy = str_replace('supplier_name', 'partners.name', $sortBy);
        }

        return Payable::query()
            ->select(['payables.id', 'payables.code', 'payables.amount', 'payables.due_date', 'payables.status', 'payables.supplier_id', 'payables.vendor_id', 'payables.created_at', 'payables.updated_at'])
            ->with(['supplier:id,name', 'vendor:id,first_name,last_name'])
            ->when($search, fn ($q, $search) => $q->whereLike('payables.code', "%$search%")
                ->orWhereLike('payables.description', "%$search%")->orWhereLike('payables.amount', "%$search%")
                ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->whereLike('partners.name', "%$search%")->orWhereLike('partners.email', "%$search%"))
                ->orWhereHas('vendor', fn ($vendorQuery) => $vendorQuery->whereLike('vendors.first_name', "%$search%")
                    ->orWhereLike('vendors.last_name', "%$search%")->orWhereLike('vendors.email', "%$search%")))
            ->when($status, fn ($q, $status) => $q->whereIn('payables.status', (array) $status))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($dueDateRange, fn ($q) => $q->whereBetween('due_date', [$dueStart, $dueEnd]))
            ->leftJoin(new Partner()->getTable(), 'payables.supplier_id', '=', 'partners.id')
            ->leftJoin(new Vendor()->getTable(), 'payables.vendor_id', '=', 'vendors.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getCreateFormData(QueryRequest $request): array
    {
        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        $supplierSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;
        $vendorSearch = $search && str_starts_with($search, 'vendor:') ? substr($search, 7) : null;

        $suppliers = Partner::select(['id', 'name'])
            ->whereType('supplier')
            ->whereIsActive(true)
            ->when($supplierSearch, fn ($q, $supplierSearch) => $q->whereLike('name', "%$supplierSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'suppliers_cursor')
            ->withQueryString();

        $vendors = Vendor::select(['id', 'first_name', 'last_name'])
            ->whereIsActive(true)
            ->when($vendorSearch, fn ($q, $vendorSearch) => $q->whereLike('first_name', "%$vendorSearch%")->orWhereLike('last_name', "%$vendorSearch%"))
            ->cursorPaginate(10, ['id', 'first_name', 'last_name'], 'vendors_cursor')
            ->withQueryString();

        return [$suppliers, $vendors];
    }

    public function storePayable(PayableRequest $request): void
    {
        $validated = $request->validated();

        $payableCount = Payable::count() + 1;
        $createdById = Auth::id();

        $shortUuid = substr((string) Str::uuid7(), 0, 8);
        $code = 'PYB-'.$shortUuid.'-'.str_pad((string) $payableCount, 6, '0', STR_PAD_LEFT);

        Payable::insert($validated + ['code' => $code, 'user_id' => $createdById]);
    }
}
