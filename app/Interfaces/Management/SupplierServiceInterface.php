<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\SupplierRequest;
use App\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface SupplierServiceInterface
{
    /**
     * Retrieve a paginated list of suppliers.
     *
     * Builds and returns a paginated collection of suppliers using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter suppliers by name, email, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of suppliers.
     */
    public function getPaginatedSuppliers(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Create a new supplier from the provided request data.
     *
     * Implementations should validate the given SupplierRequest, map it to the domain entity
     * or data model, and persist the supplier. This operation is expected to be atomic and
     * ensure data integrity. Implementations may also trigger domain events or
     * notifications as part of supplier creation.
     *
     * @param  SupplierRequest  $request  Request DTO containing supplier attributes required for creation.
     *
     * @throws \InvalidArgumentException When the supplied data is invalid.
     * @throws \RuntimeException When persistence fails or an unexpected error occurs.
     */
    public function createSupplier(SupplierRequest $request): void;

    /**
     * Update the given supplier using validated input from the request.
     *
     * @param  SupplierRequest  $request  The validated request containing supplier update data.
     * @param  Partner  $supplier  The supplier model instance to update.
     *
     * @throws \Throwable If the update cannot be completed for any other reason.
     */
    public function updateSupplier(SupplierRequest $request, Partner $supplier): void;

    /**
     * Generate a CSV export from a paginated list of suppliers.
     *
     * Accepts a LengthAwarePaginator of supplier entities and produces CSV-formatted output
     * including header row and one row per supplier.
     *
     * @param  LengthAwarePaginator  $suppliers  Paginated collection of suppliers to export.
     * @return BinaryFileResponse Response containing the generated CSV file for download.
     *
     * @throws \RuntimeException If the CSV cannot be generated.
     */
    public function generateCsvExport(LengthAwarePaginator $suppliers): BinaryFileResponse;
}
