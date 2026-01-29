<?php

namespace App\Interfaces\Product;

use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * Update the specified product using the supplied request data.
     *
     * Applies validated attributes from the ProductRequest to the
     * given Product model and persists the changes.
     *
     * @param  ProductRequest  $request  The validated request containing updated product attributes.
     * @param  Product  $product  The product model instance to update.
     *
     * @throws \Illuminate\Validation\ValidationException If the request data fails validation.
     * @throws \Throwable If an error occurs while persisting the update.
     */
    public function updateProduct(ProductRequest $request, Product $product): void;

    /**
     * Permanently delete the given product.
     *
     * This method force-deletes the provided Product instance from persistent storage,
     * bypassing any soft-delete mechanism and ensuring the record is removed permanently.
     *
     * @param  Product  $product  The product instance to permanently delete.
     */
    public function forceDeleteProduct(Product $product): void;

    /**
     * Generate a CSV export from a paginated list of products.
     *
     * Accepts a LengthAwarePaginator of product entities and produces CSV-formatted output
     * including header row and one row per product.
     *
     * @param  LengthAwarePaginator  $products  Paginated collection of products to export.
     * @return BinaryFileResponse Response containing the generated CSV file for download.
     *
     * @throws \RuntimeException If the CSV cannot be generated.
     */
    public function generateCsvExport(LengthAwarePaginator $products): BinaryFileResponse;
}
