<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\UnitOfMeasureRequest;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class UnitOfMeasureController extends Controller
{
    use ErpResourceResponses;

    private const string PAGE = 'erp/settings/units';

    private const string INDEX_ROUTE = 'erp.settings.units.index';

    private const string INDEX_PERMISSION = 'unit-of-measure.index';

    public function index(DataTableRequest $request): Response
    {
        $query = UnitOfMeasure::query();
        $archived = $request->validated('trashed') === 'only';

        if ($archived) {
            $query->onlyTrashed();
        }

        return $this->renderResourceIndex(
            $request, self::PAGE, $query->toBase(),
            ['name' => 'text', 'code' => 'text', 'quantity_decimals' => 'number'], archived: $archived,
        );
    }

    public function create(Request $request): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/create', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'New unit of measure',
            'values' => ['quantity_decimals' => 0],
        ]);
    }

    public function store(UnitOfMeasureRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => UnitOfMeasure::query()->create($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Unit of measure created.'));
    }

    public function show(Request $request, UnitOfMeasure $unit): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $unit->name,
            'details' => [
                'Name' => $unit->name,
                'Archive status' => $unit->trashed() ? 'Archived' : 'Current',
                'Code' => $unit->code,
                'Quantity decimals' => $unit->quantity_decimals,
            ],
        ]);
    }

    public function edit(Request $request, UnitOfMeasure $unit): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/edit', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Edit unit of measure',
            'id' => $unit->getKey(),
            'values' => $unit->only(['name']),
        ]);
    }

    public function update(UnitOfMeasureRequest $request, UnitOfMeasure $unit): RedirectResponse
    {
        $this->writeResource(fn () => UnitOfMeasure::query()->whereKey($unit->getKey())->lockForUpdate()->firstOrFail()->update($request->safe()->only(['name'])));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Unit of measure updated.'));
    }

    public function archive(Request $request, UnitOfMeasure $unit): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Archive '.$unit->name,
            'id' => $unit->getKey(),
        ]);
    }

    public function destroy(Request $request, UnitOfMeasure $unit): RedirectResponse
    {
        $this->writeResource(fn () => UnitOfMeasure::query()->whereKey($unit->getKey())->lockForUpdate()->firstOrFail()->delete());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Unit of measure archived.'));
    }

    public function restore(Request $request, UnitOfMeasure $unit): RedirectResponse
    {
        $this->writeResource(fn () => UnitOfMeasure::onlyTrashed()->whereKey($unit->getKey())->lockForUpdate()->firstOrFail()->restore());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Unit of measure restored.'));
    }
}
