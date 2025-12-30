<?php

namespace App\Interfaces\Management;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReceivableServiceInterface
{
    /**
     * Retrieve a paginated list of receivables with optional sorting, searching and filtering.
     *
     * @param  int|null  $perPage  Number of items per page; null to use the default pagination size.
     * @param  string|null  $sortBy  Column name to sort by; null to use the default sort column.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'); null to use the default direction.
     * @param  string|null  $search  Global search term to filter receivables (e.g., invoice number, customer).
     * @param  mixed  $filters  Additional filters to apply (implementation-specific; commonly an array of field => value pairs or a filter object).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of receivables.
     */
    public function getPaginatedReceivables(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;
}
