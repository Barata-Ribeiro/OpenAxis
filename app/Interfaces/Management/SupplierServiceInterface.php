<?php

namespace App\Interfaces\Management;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
