<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\ClientTypeEnum;
use App\Http\Requests\Management\ClientRequest;
use App\Interfaces\Management\ClientServiceInterface;
use App\Models\Client;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Log;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClientService implements ClientServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function getPaginatedClients(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $clientType = $filters['client_type'] ?? null;
        $createdAtRange = $filters['created_at'] ?? [];

        [$start, $end] = Helpers::getDateRange($createdAtRange);

        return Client::query()
            ->when($search, fn ($query, $search) => $query->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%")->orWhereLike('phone_number', "%$search%")
                ->orWhereLike('identification', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($clientType, fn ($q) => $q->whereIn('client_type', (array) $clientType))
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function createClient(ClientRequest $request): void
    {
        $validated = $request->validated();

        $clientData = Arr::only($validated, [
            'name', 'email', 'identification', 'client_type',
        ]);

        $addressData = Arr::only($validated, [
            'type', 'label', 'street', 'number', 'complement', 'neighborhood',
            'city', 'state', 'postal_code', 'country', 'is_primary',
        ]);

        DB::transaction(fn () => Client::create($clientData)->addresses()->create($addressData));
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsvExport(LengthAwarePaginator $clients): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_clients_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Name', 'Email', 'Identification', 'Client Type', 'Created At', 'Updated At', 'Deleted At'];

        fputcsv($openFile, $header, $delimiter);

        foreach ($clients as $client) {
            $row = [
                $client->id,
                $client->name,
                $client->email,
                $client->identification,
                ClientTypeEnum::tryFrom($client->client_type->value)?->label() ?? ucfirst($client->client_type),
                $client->created_at,
                $client->updated_at,
                $client->deleted_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        fclose($openFile);

        Log::info('Client: Generated clients CSV export.', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);
    }
}
