<?php

namespace App\Interfaces\Product;

use App\Http\Requests\product\ProductRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    /**
     * Retrieve a paginated list of products with optional sorting, searching, and filtering.
     *
     * This method returns a LengthAwarePaginator of products, applying the supplied
     * pagination size, sort column/direction, global search string, and any additional
     * filtering criteria. Any null arguments should cause the implementation to fall
     * back to sensible defaults (e.g. default per-page size or sort order).
     *
     * @param  int|null  $perPage  Number of items per page; when null the service default is used.
     * @param  string|null  $sortBy  Column or attribute name to sort by.
     * @param  string|null  $sortDir  Sort direction ('asc'|'desc'); when null the service default is used.
     * @param  string|null  $search  Search term to be applied to relevant product fields (name, SKU, description, etc.).
     * @param  string|array|null  $filters  Additional filters to apply (e.g. associative array of field => value, filter objects, or a query callback).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of Product models.
     */
    public function getPaginatedProducts(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Create a new product from the provided request.
     *
     * Implementations should validate the ProductRequest, map its data to a
     * product entity/model, and persist the product to the underlying storage.
     * This method does not return a value; any outcome or error should be
     * communicated by throwing an exception or via other domain-specific mechanisms.
     *
     * @param  ProductRequest  $request  The request DTO containing product data (e.g. name, price, SKU, attributes).
     *
     * @throws \InvalidArgumentException If the provided request contains invalid or missing required data.
     * @throws \RuntimeException If the product cannot be created or persisted due to a storage or domain error.
     */
    public function createProduct(ProductRequest $request): void;
}
