<?php

namespace App\Interfaces\Management;

use App\Http\Requests\QueryRequest;
use App\Models\Receivable;
use Illuminate\Contracts\Pagination\CursorPaginator;
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

    /**
     * Get detailed information for a given receivable.
     *
     * Accepts a Receivable instance (usually containing identifying information)
     * and returns a fully populated Receivable with all relevant details.
     *
     * @param  Receivable  $receivable  The receivable to fetch details for.
     * @return Receivable The detailed receivable instance.
     */
    public function getReceivableDetail(Receivable $receivable): Receivable;

    /**
     * Prepare and return the data required to populate a "create receivable" form.
     *
     * Uses the provided QueryRequest to apply filters, sorting, pagination or other query
     * options and returns a cursor-based paginator containing the form fields, related
     * entities and any metadata needed by the client.
     *
     * @param  QueryRequest  $request  Query parameters and options for building the form data.
     * @return CursorPaginator Cursor paginator with items and pagination metadata for the create form.
     */
    public function getCreateFormData(QueryRequest $request): CursorPaginator;

    /**
     * Prepare and return the data required to render the edit form for a receivable.
     *
     * This includes form field values, related entities, select/options data and any
     * contextual information derived from the provided query request (e.g. filters,
     * eager-loaded relations or other request parameters) necessary for the edit view.
     *
     * @param  Receivable  $receivable  The receivable instance being edited.
     * @param  QueryRequest  $request  Contextual query/request parameters affecting the returned data.
     * @return array<string, mixed> Associative array of data to be consumed by the edit form/view.
     */
    public function getEditFormData(Receivable $receivable, QueryRequest $request): array;
}
