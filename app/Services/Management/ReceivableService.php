<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\QueryRequest;
use App\Interfaces\Management\ReceivableServiceInterface;
use App\Models\Partner;
use App\Models\Receivable;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceivableService implements ReceivableServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function getPaginatedReceivables(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $status = $filters['status'] ?? null;

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $dueDateRange = $filters['due_date'] ?? [];
        [$dueStart, $dueEnd] = Helpers::getDateRange($dueDateRange);

        $sortByStartsWithClient = str_starts_with((string) $sortBy, 'client_name');

        if (! empty($sortBy) && $sortByStartsWithClient) {
            $sortBy = str_replace('client_name', 'partners.name', $sortBy);
        }

        return Receivable::query()
            ->select(['receivables.id', 'receivables.code', 'receivables.amount', 'receivables.due_date', 'receivables.status', 'receivables.client_id', 'receivables.created_at', 'receivables.updated_at'])
            ->with(['client:id,name'])
            ->when($search, fn ($q, $search) => $q->whereLike('receivables.code', "%$search%")
                ->orWhereLike('receivables.description', "%$search%")->orWhereLike('receivables.amount', "%$search%")
                ->orWhereHas('client', fn ($clientQuery) => $clientQuery->whereLike('partners.name', "%$search%")->orWhereLike('partners.email', "%$search%")))
            ->when($status, fn ($q, $status) => $q->whereIn('receivables.status', (array) $status))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($dueDateRange, fn ($q) => $q->whereBetween('due_date', [$dueStart, $dueEnd]))
            ->leftJoin(new Partner()->getTable(), 'receivables.client_id', '=', 'partners.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function getReceivableDetail(Receivable $receivable): Receivable
    {
        return $receivable->load(['client:id,name,email', 'bankAccount', 'salesOrder', 'user:id,name,email', 'user.media']);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreateFormData(QueryRequest $request): CursorPaginator
    {
        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        $clientSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;

        return Partner::select(['id', 'name'])
            ->whereType('client')
            ->whereIsActive(true)
            ->when($clientSearch, fn ($q, $clientSearch) => $q->whereLike('name', "%$clientSearch%")->orWhereLike('email', "%$clientSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'clients_cursor')
            ->withQueryString();
    }
}
