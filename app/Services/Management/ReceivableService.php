<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\PartnerTypeEnum;
use App\Enums\ReceivableStatusEnum;
use App\Http\Requests\Management\ReceivableRequest;
use App\Http\Requests\QueryRequest;
use App\Interfaces\Management\ReceivableServiceInterface;
use App\Models\Partner;
use App\Models\Receivable;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Number;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            ->whereType(PartnerTypeEnum::CLIENT->value)
            ->whereIsActive(true)
            ->when($clientSearch, fn ($q, $clientSearch) => $q->whereLike('name', "%$clientSearch%")->orWhereLike('email', "%$clientSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'clients_cursor')
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function getEditFormData(Receivable $receivable, QueryRequest $request): array
    {
        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        $clientSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;

        $clients = Partner::select(['id', 'name'])
            ->whereType(PartnerTypeEnum::CLIENT->value)
            ->whereIsActive(true)
            ->when($clientSearch, fn ($q, $clientSearch) => $q->whereLike('name', "%$clientSearch%")->orWhereLike('email', "%$clientSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'clients_cursor')
            ->withQueryString();

        $receivableDetail = $this->getReceivableDetail($receivable);

        return [$receivableDetail, $clients];
    }

    /**
     * {@inheritDoc}
     */
    public function updateReceivable(Receivable $receivable, ReceivableRequest $request): void
    {
        $validated = $request->validated();

        $receivable->update($validated);
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsv(LengthAwarePaginator $receivables): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_receivables_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Code', 'Client Name', 'Amount', 'Due Date', 'Status', 'Created At', 'Updated At'];

        fputcsv($openFile, $header, $delimiter);

        foreach ($receivables as $receivable) {
            $row = [
                $receivable->id,
                $receivable->code,
                $receivable->client->name ?? 'No Client',
                Number::currency($receivable->amount),
                $receivable->due_date,
                ReceivableStatusEnum::tryFrom($receivable->status->value)?->label() ?? ucfirst($receivable->status),
                $receivable->created_at,
                $receivable->updated_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }
        fclose($openFile);

        Log::info('Receivable: CSV export generated.', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);
    }
}
