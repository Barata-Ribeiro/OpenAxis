<?php

namespace App\Interfaces\Management;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PayableServiceInterface
{
    /**
     * Retrieve a paginated list of payables applying optional sorting, searching and filtering.
     *
     * @param  int|null  $perPage  Number of items per page; pass null to use the default pagination size.
     * @param  string|null  $sortBy  Column or attribute name to sort results by.
     * @param  string|null  $sortDir  Sort direction, expected 'asc' or 'desc' (case-insensitive).
     * @param  string|null  $search  Search term to filter payables by relevant fields (e.g., reference, vendor, notes).
     * @param  mixed  $filters  Additional filters to apply — typically an associative array of field => value pairs
     *                          (e.g., ['status' => 'open', 'date_from' => '2020-01-01', 'date_to' => '2020-01-31']).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of Payable models matching the criteria.
     */
    public function getPaginatedPayables(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;
}
