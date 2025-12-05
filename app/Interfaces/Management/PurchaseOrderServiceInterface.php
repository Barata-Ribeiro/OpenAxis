<?php

namespace App\Interfaces\Management;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseOrderServiceInterface
{
    /**
     * Retrieve a paginated list of purchase orders.
     *
     * Builds and returns a paginated collection of purchase orders using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter purchase orders by its properties, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     *
     * @todo Adjust rendering condition based on role/permission
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of purchase orders.
     */
    public function getPaginatedPurchaseOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;
}
