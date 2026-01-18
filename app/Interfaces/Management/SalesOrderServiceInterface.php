<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\SaleOrderRequest;
use App\Http\Requests\QueryRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SalesOrderServiceInterface
{
    /**
     * Retrieve a paginated list of sales orders.
     *
     * Builds and returns a paginated collection of sales orders using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter sales orders by its properties, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of sales orders.
     */
    public function getPaginatedSalesOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Retrieve data for populating a "create" select input, optionally filtered by a search term.
     *
     * Returns an array of options suitable for use in form select controls. Each option is typically
     * represented as an associative array with keys such as 'value' and 'label', or as an id => label map.
     *
     * @param  string|null  $search  Optional search string to filter the available options.
     * @return array<int|string, mixed> Array of select options (e.g. [['value' => ..., 'label' => ...], ...] or [id => label]).
     */
    public function getCreateDataForSelect(?string $search): array;

    /**
     * Create a new sales order from the provided request.
     *
     * @param  SaleOrderRequest  $request  The request containing sales order data.
     *
     * @throws \InvalidArgumentException If the request data is invalid.
     * @throws \RuntimeException If the sales order cannot be created or persisted.
     */
    public function createSalesOrder(SaleOrderRequest $request): void;

    /**
     * Prepare and return data needed to populate a select input when editing a sales order.
     *
     * Retrieves and formats selectable options based on the provided query request and optional search term.
     * The returned array should contain option entries (e.g., id/label pairs) and any additional metadata
     * required by the frontend (such as pagination cursors or total counts).
     *
     * @param  QueryRequest  $request  Query parameters (filters, pagination, sorting) to fetch select options.
     * @param  string|null  $search  Optional search string to filter the select results.
     * @return array Array of select options and related metadata.
     */
    public function getEditDataForSelect(QueryRequest $request, ?string $search): array;
}
