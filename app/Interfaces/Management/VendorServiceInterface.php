<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\UpdateVendorRequest;
use App\Http\Requests\Management\VendorRequest;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendorServiceInterface
{
    /**
     * Retrieve a paginated list of vendors.
     *
     * Builds and returns a paginated collection of vendors using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter vendors by full name, email, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of vendors.
     */
    public function getPaginatedVendors(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Create a new Vendor from the provided request data.
     *
     * Uses the validated values from the VendorRequest to create and persist a new
     * Vendor entity, then returns the saved Vendor instance.
     *
     * @param  VendorRequest  $request  Validated request containing vendor attributes.
     * @return Vendor The newly created and persisted Vendor entity.
     *
     * @throws \Exception If the vendor cannot be created or persistence fails.
     */
    public function createVendor(VendorRequest $request): Vendor;

    /**
     * Update the given vendor using validated input from the request.
     *
     * @param  UpdateVendorRequest  $request  The validated request containing vendor update data.
     * @param  Vendor  $vendor  The vendor model instance to update.
     *
     * @throws \Throwable If the update cannot be completed for any other reason.
     */
    public function updateVendor(UpdateVendorRequest $request, Vendor $vendor): void;
}
