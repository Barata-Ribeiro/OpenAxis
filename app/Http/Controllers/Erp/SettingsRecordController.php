<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Actions\Erp\SettingsRecords;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\SettingsRecordRequest;
use App\Models\ExpenseCategory;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Closure;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;
use Spatie\Permission\Models\Role;

final class SettingsRecordController extends Controller
{
    public function index(DataTableRequest $request): Response
    {
        $records = $this->records($request);
        $fields = $records->fields();
        $query = $records->query()->select('id')->toBase();

        foreach (array_keys($fields) as $field) {
            match ($field) {
                'is_active' => $query->selectRaw('CASE WHEN is_active THEN 1 ELSE 0 END AS is_active'),
                'is_system' => $query->selectRaw('CASE WHEN is_system THEN 1 ELSE 0 END AS is_system'),
                default => $query->addSelect($field),
            };
        }
        $table = $request->tableQuery();
        $text = array_keys(array_filter($fields, fn (string $type): bool => $type === 'text'));
        $page = $table->paginate(DB::query()->fromSub($query, 'records'), $fields, $text, 'name');

        return Inertia::render('erp/settings/index', [
            'section' => $records->section,
            'table' => $table->response($page),
        ]);
    }

    public function create(Request $request): Modal|Response
    {
        return $this->form($request, $this->records($request));
    }

    public function store(SettingsRecordRequest $request): RedirectResponse
    {
        $records = $this->records($request);
        $this->write(fn () => $records->save($request->user(), $records->query()->newModelInstance(), $request->validated()));

        return $this->redirect($request, $records);
    }

    public function show(Request $request): Modal|Response
    {
        $records = $this->records($request);
        $record = $records->query()->findOrFail((int) $request->route('record'));

        return $this->modal($request, $records, [
            'title' => $record->getAttribute('name'), 'mode' => 'show', 'details' => $records->details($record),
        ]);
    }

    public function edit(Request $request): Modal|Response
    {
        $records = $this->records($request);
        $record = $records->query()->findOrFail((int) $request->route('record'));
        $records->authorizeChange($request->user(), $record);

        return $this->form($request, $records, $record);
    }

    public function update(SettingsRecordRequest $request): RedirectResponse
    {
        $records = $this->records($request);

        $this->write(function () use ($request, $records): void {
            $record = $records->query()->lockForUpdate()->findOrFail((int) $request->route('record'));
            $records->authorizeChange($request->user(), $record);
            $data = $request->validated();

            if ($record instanceof UnitOfMeasure) {
                $data = Arr::except($data, ['code', 'quantity_decimals']);
            }

            $records->save($request->user(), $record, $data);
        });

        return $this->redirect($request, $records);
    }

    public function archive(Request $request): Modal|Response
    {
        $records = $this->records($request);
        $record = $records->query()->findOrFail((int) $request->route('record'));
        $records->authorizeChange($request->user(), $record);

        return $this->modal($request, $records, [
            'title' => ($record instanceof User || $record instanceof Role ? 'Delete ' : 'Archive ').$record->getAttribute('name'),
            'mode' => 'archive', 'id' => $record->getKey(),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $records = $this->records($request);

        $this->write(function () use ($request, $records): void {
            $record = $records->query()->lockForUpdate()->findOrFail((int) $request->route('record'));
            $records->authorizeChange($request->user(), $record);

            if (($record instanceof User && $record->is($request->user()))
                || ($record instanceof Role && $record->users()->exists())
                || ($record instanceof ExpenseCategory && $record->is_system)
                || ($record instanceof StorageLocation && $record->stockMovements()->groupBy('product_batch_id')->havingRaw('SUM(quantity_milliunits) > 0')->exists())) {
                throw new DomainException('This record is still in use and cannot be removed.');
            }

            $record->delete();
        });

        return $this->redirect($request, $records);
    }

    private function form(Request $request, SettingsRecords $records, ?Model $record = null): Modal|Response
    {
        return $this->modal($request, $records, [
            'title' => ($record === null ? 'New ' : 'Edit ').strtolower($records->title()),
            'mode' => $record === null ? 'create' : 'edit',
            'id' => $record?->getKey(),
            'values' => $records->values($record),
            'choices' => $records->choices($request->user(), $record),
            'self' => $record instanceof User && $record->is($request->user()),
        ]);
    }

    private function records(Request $request): SettingsRecords
    {
        $section = $request->route('section');
        abort_unless(is_string($section) && array_key_exists($section, SettingsRecords::MODULES), 404);

        return new SettingsRecords($section);
    }

    /** @param array<string, mixed> $props */
    private function modal(Request $request, SettingsRecords $records, array $props): Modal|Response
    {
        $props['kind'] = $records->section;
        $module = SettingsRecords::MODULES[$records->section];

        return $request->user()->can("$module.index")
            ? Inertia::modal('erp/form-modal', $props)->baseRoute("erp.settings.$records->section.index")
            : Inertia::render('erp/form', $props);
    }

    private function redirect(Request $request, SettingsRecords $records): RedirectResponse
    {
        $module = SettingsRecords::MODULES[$records->section];

        if (! $request->user()->can("$module.index")) {
            return to_route('dashboard');
        }

        $base = route("erp.settings.$records->section.index");
        $previous = $request->header(Modal::HEADER_BASE_URL) ?? url()->previous();

        return redirect()->to(strtok($previous, '?') === $base ? $previous : $base);
    }

    /** @param Closure(): void $write */
    private function write(Closure $write): void
    {
        try {
            DB::transaction($write);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages(['record' => $exception->getMessage()]);
        }
    }
}
