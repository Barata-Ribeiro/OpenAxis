<?php

namespace App\Interfaces\Product;

use App\Http\Requests\Product\ProductCategoryRequest;
use App\Http\Requests\Product\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
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

    /**
     * Create a new product category from the provided request data.
     *
     * Implementations should validate and persist the category (and any related
     * entities) to the application's data store. This method performs side effects
     * only and does not return a value; failures should be reported via exceptions.
     *
     * @param  ProductCategoryRequest  $request  Validated request containing the attributes
     *                                           required to create the product category.
     *
     * @throws \InvalidArgumentException If the provided request data is invalid.
     * @throws \RuntimeException If the category could not be persisted.
     */
    public function createCategory(ProductCategoryRequest $request): void;

    /**
     * Update the specified product category using the supplied request data.
     *
     * Applies validated attributes from the UpdateProductCategoryRequest to the
     * given ProductCategory model and persists the changes.
     *
     * @param  UpdateProductCategoryRequest  $request  The validated request containing updated category attributes.
     * @param  ProductCategory  $category  The category model instance to update.
     *
     * @throws \Illuminate\Validation\ValidationException If the request data fails validation.
     * @throws \Throwable If an error occurs while persisting the update.
     */
    public function updateCategory(UpdateProductCategoryRequest $request, ProductCategory $category): void;
}
