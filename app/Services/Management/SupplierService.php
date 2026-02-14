<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\Management\SupplierRequest;
use App\Interfaces\Management\SupplierServiceInterface;
use App\Models\Partner;
use Arr;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Log;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierService implements SupplierServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function getPaginatedSuppliers(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $is_active = $filters['is_active'][0] ?? null;

        return Partner::query()
            ->whereIn('type', ['supplier', 'both'])
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

    /**
     * {@inheritDoc}
     */
    public function createSupplier(SupplierRequest $request): void
    {
        $validated = $request->validated();

        $supplierData = Arr::only($validated, [
            'name', 'email', 'identification', 'is_active', 'phone_number', 'supplier_type',
        ]);

        if (\array_key_exists('supplier_type', $supplierData)) {
            $supplierData['type'] = $supplierData['supplier_type'];
            unset($supplierData['supplier_type']);
        }

        $addressData = Arr::only($validated, [
            'type', 'label', 'street', 'number', 'complement', 'neighborhood',
            'city', 'state', 'postal_code', 'country', 'is_primary',
        ]);

        DB::transaction(fn () => Partner::create($supplierData)->addresses()->create($addressData));
    }

    /**
     * {@inheritDoc}
     */
    public function updateSupplier(SupplierRequest $request, Partner $supplier): void
    {
        $validated = $request->validated();

        $supplierData = Arr::only($validated, [
            'name', 'email', 'identification', 'is_active', 'phone_number', 'supplier_type',
        ]);

        if (\array_key_exists('supplier_type', $supplierData)) {
            $supplierData['type'] = $supplierData['supplier_type'];
            unset($supplierData['supplier_type']);
        }

        $addressData = Arr::only($validated, [
            'type', 'label', 'street', 'number', 'complement', 'neighborhood',
            'city', 'state', 'postal_code', 'country', 'is_primary',
        ]);

        DB::transaction(function () use ($supplier, $supplierData, $addressData) {
            $supplier->update($supplierData);
            $supplier->addresses()->update($addressData);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsvExport(LengthAwarePaginator $suppliers): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_suppliers_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Name', 'Email', 'Identification', 'Is Active', 'Created At', 'Updated At', 'Deleted At'];

        fputcsv($openFile, $header, $delimiter);

        foreach ($suppliers as $supplier) {
            $row = [
                $supplier->id,
                $supplier->name,
                $supplier->email,
                $supplier->identification,
                $supplier->is_active ? 'Yes' : 'No',
                $supplier->created_at,
                $supplier->updated_at,
                $supplier->deleted_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        fclose($openFile);

        Log::info('Supplier: Generated suppliers CSV export.', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);

    }
}
