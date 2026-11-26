<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\StorageLocationRequest;
use App\Models\StorageLocation;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class StorageLocationController extends Controller
{
    use ErpResourceResponses;

    private const string PAGE = 'erp/settings/locations';

    private const string INDEX_ROUTE = 'erp.settings.locations.index';

    private const string INDEX_PERMISSION = 'storage-location.index';

    public function index(DataTableRequest $request): Response
    {
        $query = StorageLocation::query();
        $archived = $request->validated('trashed') === 'only';

        if ($archived) {
            $query->onlyTrashed();
        }

        return $this->renderResourceIndex(
            $request, self::PAGE, $query->toBase(),
            ['name' => 'text', 'code' => 'text', 'is_active' => 'number'], ['is_active'], archived: $archived,
        );
    }

    public function create(Request $request): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/create', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'New storage location',
            'values' => ['is_active' => true],
        ]);
    }

    public function store(StorageLocationRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => StorageLocation::query()->create($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Storage location created.'));
    }

    public function show(Request $request, StorageLocation $location): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $location->name,
            'details' => [
                'Name' => $location->name,
                'Archive status' => $location->trashed() ? 'Archived' : 'Current',
                'Code' => $location->code,
                'Status' => $location->is_active ? 'Active' : 'Inactive',
            ],
        ]);
    }

    public function edit(Request $request, StorageLocation $location): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/edit', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Edit storage location',
            'id' => $location->getKey(),
            'values' => $location->only(['name', 'code', 'is_active']),
        ]);
    }

    public function update(StorageLocationRequest $request, StorageLocation $location): RedirectResponse
    {
        $this->writeResource(fn () => StorageLocation::query()->whereKey($location->getKey())->lockForUpdate()->firstOrFail()->update($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Storage location updated.'));
    }

    public function archive(Request $request, StorageLocation $location): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Archive '.$location->name,
            'id' => $location->getKey(),
        ]);
    }

    public function destroy(Request $request, StorageLocation $location): RedirectResponse
    {
        $this->writeResource(function () use ($location): void {
            $locked = StorageLocation::query()->whereKey($location->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->stockMovements()->groupBy('product_batch_id')->havingRaw('SUM(quantity_milliunits) > 0')->exists()) {
                throw new DomainException('This record is still in use and cannot be removed.');
            }

            $locked->delete();
        });

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Storage location archived.'));
    }

    public function restore(Request $request, StorageLocation $location): RedirectResponse
    {
        $this->writeResource(fn () => StorageLocation::onlyTrashed()->whereKey($location->getKey())->lockForUpdate()->firstOrFail()->restore());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Storage location restored.'));
    }
}
