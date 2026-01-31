<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\PayableRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Payable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * Prepare and return the data required to render the "create" form for a payable.
     *
     * This should gather defaults, selectable option lists, related entity data,
     * validation rules and any UI metadata based on the provided query/request context.
     *
     * @param  QueryRequest  $request  Request containing query parameters, user/context info and localization
     * @return array<string,mixed> Associative array of form data keys to their values
     */
    public function getCreateFormData(QueryRequest $request): array;

    /**
     * Store a new payable using the provided request data.
     *
     * Persists a payable entity using the information contained in the given
     * PayableRequest. Implementations should validate and map request data to
     * a domain model and save it to the repository or other persistent storage.
     *
     * @param  PayableRequest  $request  The validated request containing payable data.
     *
     * @throws \Throwable If an error occurs while storing the payable.
     */
    public function storePayable(PayableRequest $request): void;

    /**
     * Populate and return a Payable with its detailed information.
     *
     * Loads related entities and computed fields required for processing or display
     * (for example: supplier, related invoices, taxes, totals, and current status).
     *
     * @param  Payable  $payable  The Payable instance to hydrate with details.
     * @return Payable The hydrated Payable instance containing populated details.
     */
    public function getPayableDetails(Payable $payable): Payable;

    /**
     * Update the given payable using validated data from the request.
     *
     * Applies changes from the provided PayableRequest to the Payable model and
     * persists the updated entity.
     *
     * @param  Payable  $payable  The payable instance to update.
     * @param  PayableRequest  $request  Validated input for updating the payable.
     *
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     * @throws \Throwable If the update cannot be persisted.
     */
    public function updatePayable(Payable $payable, PayableRequest $request): void;

    /**
     * Generate a CSV export from a paginated list of payables.
     *
     * Accepts a LengthAwarePaginator of payable entities and produces CSV-formatted output
     * including header row and one row per payable.
     *
     * @param  LengthAwarePaginator  $payables  Paginated collection of payables to export.
     * @return BinaryFileResponse Response containing the generated CSV file for download.
     *
     * @throws \RuntimeException If the CSV cannot be generated.
     */
    public function generateCsvExport(LengthAwarePaginator $payables): BinaryFileResponse;
}
