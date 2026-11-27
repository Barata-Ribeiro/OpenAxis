<?php

declare(strict_types=1);

namespace App\Concerns\Erp;

use App\Http\Requests\Erp\DataTableRequest;
use Closure;
use DomainException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;

trait ErpResourceResponses
{
    /**
     * @param  array<literal-string, 'text'|'number'>  $fields
     * @param  list<'is_active'|'is_system'>  $booleanFields
     */
    private function renderResourceIndex(DataTableRequest $request, string $page, Builder $query, array $fields, array $booleanFields = [], ?bool $archived = null): Response
    {
        $query->select('id');

        if ($archived !== null) {
            $query->addSelect('deleted_at');
        }

        foreach (array_keys($fields) as $field) {
            if (in_array($field, $booleanFields, true)) {
                $query->selectRaw("CASE WHEN $field THEN 1 ELSE 0 END AS $field");
            } else {
                $query->addSelect($field);
            }
        }

        $table = $request->tableQuery();
        $searchable = array_keys(array_filter($fields, fn (string $type): bool => $type === 'text'));
        $records = $table->paginate(DB::query()->fromSub($query, 'records'), $fields, $searchable, 'name');

        return Inertia::render($page.'/index', [
            'table' => $table->response($records),
            ...($archived === null ? [] : ['archived' => $archived]),
        ]);
    }

    /** @param array<string, mixed> $props */
    private function renderResource(Request $request, string $page, string $index, string $permission, array $props): Modal|Response
    {
        if ($request->user()->can($permission)) {
            return Inertia::modal($page, [...$props, 'modal' => true])->baseRoute($index);
        }

        return Inertia::render($page, $props);
    }

    private function resourceRedirect(Request $request, string $index, string $permission, string $message): RedirectResponse
    {
        Inertia::flash('toast', ['type' => 'success', 'message' => $message]);

        if (! $request->user()->can($permission)) {
            return to_route('dashboard');
        }

        $base = route($index);
        $previous = $request->header(Modal::HEADER_BASE_URL) ?? url()->previous();

        return redirect()->to(strtok($previous, '?') === $base ? $previous : $base);
    }

    /** @param Closure(): mixed $write */
    private function writeResource(Closure $write): void
    {
        try {
            DB::transaction($write, attempts: 3);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages(['record' => $exception->getMessage()]);
        }
    }
}
