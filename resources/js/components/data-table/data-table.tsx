import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { features } from '@/hooks/data-table/features';
import { useAppTable } from '@/hooks/data-table/table';
import { useDebouncedCallback } from '@/hooks/use-debounce-callback';
import { cn } from '@/lib/utils';
import type { ExtendedColumnFilter } from '@/types/data-table';
import { router } from '@inertiajs/react';
import type {
    Column,
    ColumnDef,
    ColumnPinningState,
    PaginationState,
    SortingState,
    Updater,
} from '@tanstack/react-table';
import { ArrowDown, ArrowUp, ChevronsUpDown, Search } from 'lucide-react';
import { useState } from 'react';

export type ServerTableData<TData> = {
    rows: TData[];
    total: number;
    page: number;
    perPage: number;
    search: string;
    sorting: SortingState;
    filters: ExtendedColumnFilter[];
};

type Props<TData extends { id: string | number }> = {
    columns: ColumnDef<typeof features, TData>[];
    initialColumnPinning?: ColumnPinningState;
    result: ServerTableData<TData>;
    url: string;
    label: string;
};

function getCommonPinningStyles<TData extends { id: string | number }>(
    column: Column<typeof features, TData>,
): React.CSSProperties | undefined {
    const pinned = column.getIsPinned();
    if (!pinned) return undefined;

    const size = column.getSize();
    return {
        insetInlineStart: pinned === 'start' ? column.getStart('start') : undefined,
        insetInlineEnd: pinned === 'end' ? column.getAfter('end') : undefined,
        borderInlineEnd: pinned === 'start' && column.getIsLastColumn('start') ? '1px solid var(--border)' : undefined,
        borderInlineStart: pinned === 'end' && column.getIsFirstColumn('end') ? '1px solid var(--border)' : undefined,
        width: size,
        minWidth: size,
        maxWidth: size,
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        position: 'sticky',
        zIndex: 1,
    };
}

function update<T>(updater: Updater<T>, previous: T): T {
    return typeof updater === 'function' ? (updater as (value: T) => T)(previous) : updater;
}

function activeFilter(filter: ExtendedColumnFilter) {
    if (filter.operator === 'isEmpty' || filter.operator === 'isNotEmpty') return true;
    const hasValue = (value: unknown) => value !== undefined && value !== null && value !== '';
    return Array.isArray(filter.value) ? filter.value.some(hasValue) : hasValue(filter.value);
}

export default function DataTable<TData extends { id: string | number }>({
    columns,
    initialColumnPinning = { start: [], end: ['actions'] },
    result,
    url,
    label,
}: Readonly<Props<TData>>) {
    const [search, setSearch] = useState(result.search);
    const [filters, setFilters] = useState<ExtendedColumnFilter[]>(() =>
        result.filters.map((filter, index) => ({
            ...filter,
            filterId: filter.filterId ?? String(index),
        })),
    );
    const [sorting, setSorting] = useState(result.sorting);
    const [error, setError] = useState('');
    const pagination = { pageIndex: result.page - 1, pageSize: result.perPage };

    const visit = useDebouncedCallback(
        (next: PaginationState, term: string, nextFilters: ExtendedColumnFilter[], nextSorting: SortingState) => {
            const query = new URL(url, window.location.origin);
            query.searchParams.set('table[page]', String(next.pageIndex + 1));
            query.searchParams.set('table[perPage]', String(next.pageSize));
            if (term.trim()) query.searchParams.set('table[search]', term.trim());

            nextFilters.filter(activeFilter).forEach((filter, index) => {
                const key = 'table[filters][' + index + ']';
                query.searchParams.set(key + '[id]', filter.id);
                query.searchParams.set(key + '[operator]', filter.operator ?? 'includesString');
                query.searchParams.set(key + '[joinOperator]', filter.joinOperator ?? 'and');
                if (filter.filterId) query.searchParams.set(key + '[filterId]', filter.filterId);
                if (Array.isArray(filter.value)) {
                    filter.value.forEach((value, slot) =>
                        query.searchParams.set(key + '[value][' + slot + ']', String(value ?? '')),
                    );
                } else if (typeof filter.value === 'string' || typeof filter.value === 'number') {
                    query.searchParams.set(key + '[value]', String(filter.value));
                }
            });

            nextSorting.forEach((sort, index) => {
                const key = 'table[sorting][' + index + ']';
                query.searchParams.set(key + '[id]', sort.id);
                query.searchParams.set(key + '[desc]', sort.desc ? '1' : '0');
            });

            router.get(
                query.pathname + query.search,
                {},
                {
                    only: ['table'],
                    replace: true,
                    preserveState: true,
                    preserveScroll: true,
                    viewTransition: false,
                    onSuccess: () => setError(''),
                    onError: (errors) => setError(Object.values(errors).join(' ')),
                },
            );
        },
        300,
    );

    const table = useAppTable({
        key: label,
        columns,
        data: result.rows,
        rowCount: result.total,
        pageCount: Math.max(1, Math.ceil(result.total / result.perPage)),
        manualFiltering: true,
        manualSorting: true,
        manualPagination: true,
        autoResetPageIndex: false,
        enableGrouping: false,
        enableRowSelection: false,
        sortDescFirst: false,
        maxMultiSortColCount: 12,
        initialState: { columnPinning: initialColumnPinning },
        state: { pagination, sorting, columnFilters: filters, globalFilter: search },
        onPaginationChange: (value) => {
            const next = update(value, pagination);
            visit(next.pageSize === pagination.pageSize ? next : { ...next, pageIndex: 0 }, search, filters, sorting);
        },
        onSortingChange: (value) => {
            const next = update(value, sorting);
            setSorting(next);
            visit({ ...pagination, pageIndex: 0 }, search, filters, next);
        },
        onColumnFiltersChange: (value) => {
            const next = update(value, filters) as ExtendedColumnFilter[];
            if (next.length > 20) return;
            setFilters(next);
            visit({ ...pagination, pageIndex: 0 }, search, next, sorting);
        },
    });

    const { columnOrder, columnPinning, columnSizing, columnVisibility } = table.state;
    const columnLayout = [columnOrder, columnPinning, columnSizing, columnVisibility];
    const headers = table.getLeafHeaders();
    const primarySort = sorting.find((sort) => headers.some((header) => header.column.id === sort.id));
    const rowHeader = (headers.find((header) => header.column.accessorFn) ?? headers[0])?.column.id;

    return (
        <table.AppTable>
            <Card className="mx-auto w-full min-w-0">
                <CardHeader className="flex flex-wrap items-center gap-2">
                    <div className="relative w-full max-w-sm">
                        <Search
                            aria-hidden
                            className="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            type="search"
                            aria-label={`Search ${label.toLowerCase()}`}
                            placeholder="Search records…"
                            className="pl-8"
                            maxLength={200}
                            value={search}
                            onChange={(event) => {
                                setSearch(event.target.value);
                                visit({ ...pagination, pageIndex: 0 }, event.target.value, filters, sorting);
                            }}
                        />
                    </div>
                    <table.FilterList />
                    <table.SortList />
                    <table.ViewOptions />
                    {(search || filters.length > 0 || sorting.length > 0) && (
                        <Button
                            variant="ghost"
                            size="sm"
                            onPress={() => {
                                setSearch('');
                                setFilters([]);
                                setSorting([]);
                                visit({ ...pagination, pageIndex: 0 }, '', [], []);
                            }}
                        >
                            Reset
                        </Button>
                    )}
                </CardHeader>
                <CardContent className="min-w-0 border-y px-0">
                    {error && (
                        <p role="alert" className="p-3 text-sm text-destructive">
                            {error}
                        </p>
                    )}
                    <Table
                        aria-label={label}
                        className="border-separate border-spacing-0"
                        sortDescriptor={
                            primarySort
                                ? { column: primarySort.id, direction: primarySort.desc ? 'descending' : 'ascending' }
                                : undefined
                        }
                        onSortChange={({ column, direction }) =>
                            table.getColumn(String(column))?.toggleSorting(direction === 'descending')
                        }
                    >
                        <TableHeader columns={headers} dependencies={[...columnLayout, sorting]}>
                            {(header) => {
                                const label = header.column.columnDef.meta?.label ?? header.column.id;
                                const sorted = header.column.getIsSorted();
                                const Indicator =
                                    sorted === 'asc' ? ArrowUp : sorted === 'desc' ? ArrowDown : ChevronsUpDown;
                                return (
                                    <TableHead
                                        id={header.column.id}
                                        isRowHeader={header.column.id === rowHeader}
                                        allowsSorting={primarySort?.id === header.column.id}
                                        textValue={label}
                                        className="border-b bg-card px-3"
                                        style={getCommonPinningStyles(header.column)}
                                    >
                                        {header.column.getCanSort() ? (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="-ml-2"
                                                onPress={() => header.column.toggleSorting()}
                                            >
                                                {label}
                                                <Indicator aria-hidden className="size-3.5 text-muted-foreground" />
                                            </Button>
                                        ) : (
                                            label
                                        )}
                                    </TableHead>
                                );
                            }}
                        </TableHeader>
                        <TableBody
                            items={table.getRowModel().rows}
                            dependencies={columnLayout}
                            renderEmptyState={() => 'No records found.'}
                        >
                            {(row) => (
                                <TableRow
                                    id={'row:' + row.id}
                                    columns={row.getVisibleCells()}
                                    dependencies={columnLayout}
                                    className="group/table-row"
                                >
                                    {(cell) => {
                                        const pinningStyles = getCommonPinningStyles(cell.column);
                                        return (
                                            <TableCell
                                                id={'cell:' + cell.id}
                                                className={cn(
                                                    'border-b px-3 group-last/table-row:border-b-0',
                                                    pinningStyles &&
                                                        'bg-card group-hover/table-row:bg-muted group-data-selected/table-row:bg-muted',
                                                )}
                                                style={pinningStyles}
                                            >
                                                <table.AppCell cell={cell}>
                                                    {(appCell) => <appCell.FlexRender />}
                                                </table.AppCell>
                                            </TableCell>
                                        );
                                    }}
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter>
                    <table.Pagination pageSizeOptions={[10, 20, 50, 100]} />
                </CardFooter>
            </Card>
        </table.AppTable>
    );
}
