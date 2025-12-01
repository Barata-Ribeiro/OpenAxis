<?php

namespace App\Interfaces\Management;

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
}
