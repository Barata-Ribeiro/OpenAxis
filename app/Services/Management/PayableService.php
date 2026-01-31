<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\PartnerTypeEnum;
use App\Http\Requests\Management\PayableRequest;
use App\Http\Requests\QueryRequest;
use App\Interfaces\Management\PayableServiceInterface;
use App\Models\Partner;
use App\Models\Payable;
use App\Models\Vendor;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Log;
use Number;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PayableService implements PayableServiceInterface
{
    /**
     * {@inheritDoc}
     */
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
            ->with(['supplier:id,name,email', 'vendor:id,first_name,last_name'])
            ->when($search, fn (Builder $q, $search) => $q->whereLike('payables.code', "%$search%")
                ->orWhereLike('payables.description', "%$search%")->orWhereLike('payables.amount', "%$search%")
                ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->whereLike('partners.name', "%$search%")->orWhereLike('partners.email', "%$search%"))
                ->orWhereHas('vendor', fn ($vendorQuery) => $vendorQuery->whereLike('vendors.first_name', "%$search%")
                    ->orWhereLike('vendors.last_name', "%$search%")->orWhereRaw("CONCAT(vendors.first_name, ' ', vendors.last_name) LIKE ?", ["%$search%"])
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('users.name', "%$search%")->orWhereLike('users.email', "%$search%"))))
            ->when($status, fn (Builder $q, $status) => $q->whereIn('payables.status', (array) $status))
            ->when($createdAtRange, fn (Builder $q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($dueDateRange, fn (Builder $q) => $q->whereBetween('due_date', [$dueStart, $dueEnd]))
            ->leftJoin(new Partner()->getTable(), 'payables.supplier_id', '=', 'partners.id')
            ->leftJoin(new Vendor()->getTable(), 'payables.vendor_id', '=', 'vendors.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function getCreateFormData(QueryRequest $request): array
    {
        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        $supplierSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;
        $vendorSearch = $search && str_starts_with($search, 'vendor:') ? substr($search, 7) : null;

        $suppliers = Partner::select(['id', 'name'])
            ->whereType(PartnerTypeEnum::SUPPLIER->value)
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

    /**
     * {@inheritDoc}
     */
    public function storePayable(PayableRequest $request): void
    {
        $validated = $request->validated();

        $payableCount = Payable::count() + 1;
        $createdById = Auth::id();

        $shortUuid = substr((string) Str::uuid7(), 0, 8);
        $code = 'PYB-'.$shortUuid.'-'.str_pad((string) $payableCount, 6, '0', STR_PAD_LEFT);

        Payable::insert($validated + ['code' => $code, 'user_id' => $createdById]);
    }

    public function getPayableDetails(Payable $payable): Payable
    {
        return $payable->load(['supplier', 'vendor', 'vendor.user', 'vendor.user.media', 'bankAccount', 'salesOrder', 'user:id,name,email', 'user.media']);
    }

    /**
     * {@inheritDoc}
     */
    public function updatePayable(Payable $payable, PayableRequest $request): void
    {
        $validated = $request->validated();

        $payable->update($validated);
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsvExport(LengthAwarePaginator $payables): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_payables_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Code', 'Supplier', 'Amount', 'Due Date', 'Status', 'Vendor', 'Created At', 'Updated At'];

        fputcsv($openFile, $header, $delimiter);

        foreach ($payables as $payable) {
            $row = [
                $payable->id,
                $payable->code,
                $payable->supplier->name ?? 'No Supplier',
                Number::currency($payable->amount),
                $payable->due_date->format('Y-m-d'),
                $payable->status->label(),
                $payable->vendor?->full_name,
                $payable->created_at,
                $payable->updated_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        Log::info('Payable: CSV export generated.', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);
    }
}
