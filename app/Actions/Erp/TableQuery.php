<?php

declare(strict_types=1);

namespace App\Actions\Erp;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

use function count;
use function in_array;
use function is_array;
use function is_int;
use function is_string;

/**
 * @phpstan-type Filter array{id: string, operator: string, value?: mixed, filterId?: string, joinOperator?: string}
 * @phpstan-type Sort array{id: string, desc: bool|0|1|'0'|'1'}
 */
final class TableQuery
{
    private const array TEXT = ['includesString', 'notIncludesString', 'equalsString', 'notEqualsString', 'startsWith', 'endsWith'];

    private const array COMPARE = [
        'equals' => '=', 'notEquals' => '!=', 'greaterThan' => '>',
        'greaterThanOrEqualTo' => '>=', 'lessThan' => '<', 'lessThanOrEqualTo' => '<=',
    ];

    private const array EMPTY = ['isEmpty', 'isNotEmpty'];

    /** @param array{search?: string|null, filters?: list<Filter>, sorting?: list<Sort>, page?: numeric-string|int, perPage?: numeric-string|int} $input */
    public function __construct(private readonly array $input) {}

    /** @return list<string> */
    public static function operators(): array
    {
        return [...self::TEXT, ...array_keys(self::COMPARE), ...self::EMPTY, 'inRange', 'isRelativeToToday'];
    }

    /**
     * @param  array<literal-string, 'text'|'date'|'number'|'select'>  $fields  Column allowlist, keyed by the ID sent by TanStack Table.
     * @param  list<literal-string>  $searchable
     * @return LengthAwarePaginator<int, stdClass>
     */
    public function paginate(Builder $query, array $fields, array $searchable, string $default = 'id', bool $descending = false): LengthAwarePaginator
    {
        if (($term = $this->input['search'] ?? '') !== '' && $searchable !== []) {
            $query->where(function (Builder $group) use ($searchable, $term): void {
                foreach ($searchable as $field) {
                    $this->text($group, $field, 'includesString', $term, 'or');
                }
            });
        }

        $filters = collect($this->input['filters'] ?? [])->groupBy('id');

        if (array_diff_key($filters->all(), $fields) !== []) {
            $this->invalid('filters');
        }

        foreach ($fields as $field => $type) {
            $conditions = $filters->get($field);

            if ($conditions === null) {
                continue;
            }

            $query->where(function (Builder $group) use ($conditions, $field, $type): void {
                $join = ($conditions->first()['joinOperator'] ?? '') === 'or' ? 'or' : 'and';

                foreach ($conditions as $condition) {
                    $group->where(
                        fn (Builder $clause) => $this->filter($clause, $field, $type, $condition),
                        boolean: $join,
                    );
                }
            });
        }

        foreach ($this->input['sorting'] ?? [] as $sort) {
            if (! isset($fields[$sort['id']])) {
                $this->invalid('sorting');
            }

            $query->orderBy($sort['id'], $sort['desc'] ? 'desc' : 'asc');
        }

        $query->orderBy($default, $descending ? 'desc' : 'asc');

        if ($default !== 'id') {
            $query->orderBy('id');
        }

        $size = (int) ($this->input['perPage'] ?? 10);
        $page = (int) ($this->input['page'] ?? 1);
        $results = $query->paginate($size, ['*'], 'page', $page);

        if ($page > $results->lastPage()) {
            return $query->paginate($size, ['*'], 'page', $results->lastPage());
        }

        return $results;
    }

    /**
     * @param  LengthAwarePaginator<int, stdClass>  $page
     * @param  list<array<string, mixed>|stdClass>|null  $rows
     * @return array{search: string, filters: list<Filter>, sorting: list<array{id: string, desc: bool}>, rows: list<array<string, mixed>|stdClass>, total: int, page: int, perPage: int}
     */
    public function response(LengthAwarePaginator $page, ?array $rows = null): array
    {
        return [
            'search' => $this->input['search'] ?? '',
            'filters' => array_map(function (array $filter): array {
                $value = $filter['value'] ?? null;

                if ($filter['operator'] === 'inRange' && is_array($value)) {
                    $filter['value'] = [$value[0] ?? null, $value[1] ?? null];
                }

                return $filter;
            }, $this->input['filters'] ?? []),
            'sorting' => array_map(fn (array $sort): array => [
                'id' => $sort['id'],
                'desc' => (bool) $sort['desc'],
            ], $this->input['sorting'] ?? []),
            'rows' => $rows ?? array_values($page->items()),
            'total' => $page->total(),
            'page' => $page->currentPage(),
            'perPage' => $page->perPage(),
        ];
    }

    /**
     * @param  literal-string  $field
     * @param  'text'|'date'|'number'|'select'  $type
     * @param  Filter  $condition
     */
    private function filter(Builder $query, string $field, string $type, array $condition): void
    {
        $operator = $condition['operator'];
        $value = $condition['value'] ?? null;
        $allowed = match ($type) {
            'text' => [...self::TEXT, ...self::EMPTY],
            'select' => ['equals', 'notEquals', ...self::EMPTY],
            'number' => [...array_keys(self::COMPARE), 'inRange'],
            'date' => [...array_keys(self::COMPARE), ...self::EMPTY, 'inRange', 'isRelativeToToday'],
        };

        if (! in_array($operator, $allowed, true)) {
            $this->invalid('filters');
        }

        if (in_array($operator, self::EMPTY, true)) {
            $empty = $operator === 'isEmpty';
            $query->whereNull($field, 'and', ! $empty);

            if (in_array($type, ['text', 'select'], true)) {
                $empty ? $query->orWhere($field, '') : $query->where($field, '!=', '');
            }

            return;
        }

        if ($type === 'text') {
            $this->text($query, $field, $operator, (string) $this->value($value, $type));

            return;
        }

        if ($type === 'select') {
            $values = is_array($value) && array_is_list($value) ? $value : [$value];

            if (count($values) < 1 || count($values) > 50) {
                $this->invalid('filters');
            }

            $values = array_map(fn (mixed $item): string => (string) $this->value($item, $type), $values);
            $query->whereIn($field, $values, 'and', $operator === 'notEquals');

            if ($operator === 'notEquals') {
                $query->orWhereNull($field);
            }

            return;
        }

        if ($operator === 'isRelativeToToday') {
            $today = today();
            $relative = function (mixed $days) use ($today): string {
                if ((! is_int($days) && ! is_string($days))
                    || filter_var($days, FILTER_VALIDATE_INT, ['options' => ['min_range' => -36_500, 'max_range' => 36_500]]) === false) {
                    $this->invalid('filters');
                }

                return $today->addDays((int) $days)->toDateString();
            };

            if (! is_array($value)) {
                $query->whereDate($field, $relative($value));

                return;
            }

            $value = array_map($relative, $value);
            $operator = 'inRange';
        }

        if ($operator === 'inRange') {
            if (! is_array($value) || array_diff(array_keys($value), [0, 1]) !== []) {
                $this->invalid('filters');
            }

            foreach ([0 => '>=', 1 => '<='] as $index => $comparison) {
                if (isset($value[$index])) {
                    $this->compare($query, $field, $type, $comparison, $this->value($value[$index], $type));
                }
            }

            return;
        }

        $comparison = self::COMPARE[$operator] ?? $this->invalid('filters');
        $this->compare($query, $field, $type, $comparison, $this->value($value, $type));

        if ($operator === 'notEquals') {
            $query->orWhereNull($field);
        }
    }

    private function compare(Builder $query, string $field, string $type, string $operator, string|float|int $value): void
    {
        if ($type === 'date') {
            $query->whereDate($field, $operator, (string) $value);

            return;
        }

        $query->where($field, $operator, $value);
    }

    private function value(mixed $value, string $type): string|float|int
    {
        $valid = match ($type) {
            'number' => is_numeric($value) && is_finite((float) $value),
            'date' => is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value) && date_parse($value)['warning_count'] === 0,
            default => is_string($value) && mb_strlen($value) <= 200,
        };

        if (! $valid) {
            $this->invalid('filters');
        }

        return $type === 'number' ? $value + 0 : $value;
    }

    /** @param literal-string $field */
    private function text(Builder $query, string $field, string $operator, string $value, string $join = 'and'): void
    {
        $quote = $query->getGrammar() instanceof MySqlGrammar ? '`' : '"';
        $column = "LOWER(COALESCE($quote$field$quote, ''))";
        $value = Str::lower($value);

        if (in_array($operator, ['equalsString', 'notEqualsString'], true)) {
            $query->whereRaw("$column ".($operator === 'equalsString' ? '=' : '!=').' ?', [$value], $join);

            return;
        }

        $escaped = strtr($value, ['!' => '!!', '%' => '!%', '_' => '!_']);
        $pattern = match ($operator) {
            'startsWith' => $escaped.'%',
            'endsWith' => '%'.$escaped,
            default => '%'.$escaped.'%',
        };

        $comparison = $operator === 'notIncludesString' ? 'NOT LIKE' : 'LIKE';
        $query->whereRaw("$column $comparison ? ESCAPE '!'", [$pattern], $join);
    }

    private function invalid(string $field): never
    {
        throw ValidationException::withMessages(["table.$field" => 'The table contains an invalid column or filter value.']);
    }
}
