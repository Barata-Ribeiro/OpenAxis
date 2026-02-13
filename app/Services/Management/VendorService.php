<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\Management\UpdateVendorRequest;
use App\Http\Requests\Management\VendorRequest;
use App\Interfaces\Management\VendorServiceInterface;
use App\Models\User;
use App\Models\Vendor;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Log;
use Number;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VendorService implements VendorServiceInterface
{
    /**
     * {@inheritDoc}
     */
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
            ->when($search, fn (Builder $q, $search) => $q->whereLike('vendors.first_name', "%$search%")
                ->orWhereLike('vendors.last_name', "%$search%")->orWhereLike('vendors.phone_number', "%$search%")
                ->orWhereRaw("CONCAT(vendors.first_name, ' ', vendors.last_name) LIKE ?", ["%$search%"])
                ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->whereLike('users.name', "%$search%")->orWhereLike('users.email', "%$search%")))
            ->when($vendorUserEmail, fn (Builder $q) => $q->whereHas('user', fn (Builder $userQuery) => $userQuery->whereIn('users.email', $vendorUserEmail)))
            ->when($createdAtRange, fn (Builder $q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($is_active, fn (Builder $query) => $query->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->leftJoin((new User)->getTable(), 'vendors.user_id', '=', 'users.id')
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function createVendor(VendorRequest $request): Vendor
    {
        $validated = $request->validated();

        return Vendor::create($validated);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVendor(UpdateVendorRequest $request, Vendor $vendor): void
    {
        $validated = $request->validated();

        $vendor->update($validated);
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsvExport(LengthAwarePaginator $vendors): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_vendors_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Name', 'Email', 'Comission Rate', 'Is Active', 'Created At', 'Updated At', 'Deleted At'];

        fputcsv($openFile, $header, $delimiter);

        foreach ($vendors as $vendor) {
            $row = [
                $vendor->id,
                $vendor->full_name,
                $vendor->user->email ?? 'No Account Associated',
                Number::percentage($vendor->commission_rate),
                $vendor->is_active ? 'Yes' : 'No',
                $vendor->created_at,
                $vendor->updated_at,
                $vendor->deleted_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        fclose($openFile);

        Log::info('Vendors: Generated vendors CSV export.', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);
    }
}
