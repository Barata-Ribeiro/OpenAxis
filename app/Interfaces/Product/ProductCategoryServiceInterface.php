<?php

namespace App\Interfaces\Product;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductCategoryServiceInterface
{
    /**
     * Get filtered product categories.
     *
     * @param  string|array|null  $filters
     */
    /**
     * Retrieve a paginated list of product categories with optional sorting, searching and filtering.
     *
     * @param  int|null  $perPage  Number of items per page. If null, the service's default pagination size will be used.
     * @param  string|null  $sortBy  Column or attribute to sort by.
     * @param  string|null  $sortDir  Sort direction, typically 'asc' or 'desc'.
     * @param  string|null  $search  Search term to filter categories by name or other searchable fields.
     * @param  string|array|null  $filters  Additional filters to apply (e.g. associative array of field => value, filter objects, or a query callback).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of categories matching the provided criteria.
     */
    public function getPaginatedCategories(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;
}
