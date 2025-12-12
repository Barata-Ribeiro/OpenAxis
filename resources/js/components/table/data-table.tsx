import DataTableExportData from '@/components/table/data-table-export-data';
import DataTablePagination from '@/components/table/data-table-pagination';
import DataTableToolbar from '@/components/table/data-table-toolbar';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import useIsMounted from '@/hooks/use-mounted';
import { buildParams } from '@/lib/utils';
import type { PaginationMeta } from '@/types';
import type { RouteDefinition } from '@/wayfinder';
import { Link, router, usePage } from '@inertiajs/react';
import {
    type Column,
    type ColumnDef,
    type ColumnFiltersState,
    type SortingState,
    type Updater,
    type VisibilityState,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { ClipboardPlusIcon } from 'lucide-react';
import { type CSSProperties, useCallback, useEffect, useEffectEvent, useMemo, useState } from 'react';

interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: PaginationMeta<TData[]>['data'];
    pagination: Omit<PaginationMeta<TData[]>, 'data'>;
    createRoute?: RouteDefinition<'get'>;
    exportables?: Partial<Record<'csvRoute' | 'pdfRoute', RouteDefinition<'get'>>>;
}

type FilterValue = string | string[] | null;

function getCommonPinningStyles<TData>({
    column,
    withBorder = false,
}: {
    column: Column<TData>;
    withBorder?: boolean;
}): CSSProperties {
    const isPinned = column.getIsPinned();
    const isLastLeftPinnedColumn = isPinned === 'left' && column.getIsLastColumn('left');
    const isFirstRightPinnedColumn = isPinned === 'right' && column.getIsFirstColumn('right');

    const leftPinnedBoxShadow = '-4px 0 4px -4px var(--border) inset';
    const rightPinnedBoxShadow = '4px 0 4px -4px var(--border) inset';

    const rightPinnedShadow = isFirstRightPinnedColumn ? rightPinnedBoxShadow : undefined;
    const pinnedColumnShadow = isLastLeftPinnedColumn ? leftPinnedBoxShadow : rightPinnedShadow;

    return {
        boxShadow: withBorder ? pinnedColumnShadow : undefined,
        left: isPinned === 'left' ? `${column.getStart('left')}px` : undefined,
        right: isPinned === 'right' ? `${column.getAfter('right')}px` : undefined,
        opacity: isPinned ? 0.97 : 1,
        position: isPinned ? 'sticky' : 'relative',
        background: 'var(--background)',
        width: column.getSize(),
        zIndex: isPinned ? 1 : undefined,
    };
}

export function DataTable<TData, TValue>({
    columns,
    data,
    pagination,
    createRoute,
    exportables,
}: Readonly<DataTableProps<TData, TValue>>) {
    const page = usePage();
    const isMounted = useIsMounted();
    const [path] = useState(pagination.path);

    const onFilteringNavigating = useEffectEvent((filtersParams: string | undefined) => {
        if (!path) return;

        router.get(path, buildParams({ filters: filtersParams }), {
            preserveState: true,
            replace: true,
        });
    });

    const onSortingNavigating = useEffectEvent((sortBy: string | undefined, sortDir: string | undefined) => {
        if (!path) return;

        router.get(path, buildParams({ sort_by: sortBy, sort_dir: sortDir }), {
            preserveState: true,
            replace: true,
        });
    });

    const params = useMemo(() => {
        try {
            const urlObj = new URL(page.url ?? globalThis.location.href, globalThis.location.origin);
            return new URLSearchParams(urlObj.search);
        } catch {
            return new URLSearchParams(globalThis.location.search);
        }
    }, [page.url]);

    const [sorting, setSorting] = useState<SortingState>(() => {
        const sort_by = params.get('sort_by');
        const sort_dir = params.get('sort_dir');
        if (!sort_by) return [];

        return [{ id: sort_by, desc: sort_dir === 'desc' }];
    });

    const [filterValues, setFilterValues] = useState<Record<string, FilterValue>>(() => {
        const rawFilters = params.get('filters');
        if (!rawFilters) return {};

        const obj: Record<string, FilterValue> = {};

        const tokens = rawFilters.split(',');
        let currentKey = '';
        let currentValueParts: string[] = [];

        for (const token of tokens) {
            const idx = token.indexOf(':');
            if (idx === -1) {
                if (currentKey) currentValueParts.push(token);
            } else {
                if (currentKey) obj[currentKey] = currentValueParts.join(',');
                currentKey = token.substring(0, idx);
                currentValueParts = [token.substring(idx + 1)];
            }
        }

        if (currentKey) obj[currentKey] = currentValueParts.join(',');

        const decodedFilters: Record<string, FilterValue> = {};

        for (const [k, v] of Object.entries(obj)) {
            const key = decodeURIComponent(k);
            const value = decodeURIComponent(String(v));
            decodedFilters[key] = value;
        }

        return decodedFilters;
    });

    const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>(
        useMemo(() => {
            return Object.entries(filterValues).reduce<ColumnFiltersState>((filters, [key, value]) => {
                if (value !== null) {
                    let processedValue;

                    if (Array.isArray(value)) processedValue = value;
                    else if (typeof value === 'string' && value.includes(',')) {
                        processedValue = value
                            .split(',')
                            .map((v) => v.trim())
                            .filter(Boolean);
                    } else processedValue = [value];

                    filters.push({
                        id: key,
                        value: processedValue,
                    });
                }
                return filters;
            }, []);
        }, [filterValues]),
    );

    const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});

    const onColumnFiltersChange = useCallback((updaterOrValue: Updater<ColumnFiltersState>) => {
        setColumnFilters((prev) => {
            const next = typeof updaterOrValue === 'function' ? updaterOrValue(prev) : updaterOrValue;

            const filterUpdates = next.reduce<Record<string, FilterValue>>((acc, f) => {
                const val = f.value;
                let normalized: FilterValue;

                if (val == null) normalized = null;
                else if (Array.isArray(val)) normalized = val as string[];
                else if (typeof val === 'string') normalized = val;
                else normalized = JSON.stringify(val);

                acc[f.id] = normalized;
                return acc;
            }, {});

            for (const prevFilter of prev) {
                if (!next.some((filter) => filter.id === prevFilter.id)) {
                    filterUpdates[prevFilter.id] = null;
                }
            }

            setFilterValues((prevValues) => ({
                ...prevValues,
                ...filterUpdates,
            }));

            return next;
        });
    }, []);

    const table = useReactTable({
        columns,
        data,
        getCoreRowModel: getCoreRowModel(),
        manualPagination: true, // turn off client-side pagination
        manualSorting: true, // turn off client-side sorting
        manualFiltering: true, // turn off client-side filtering
        pageCount: pagination.last_page ?? Math.ceil((pagination.total ?? 0) / (pagination.per_page ?? 1)),
        initialState: {
            pagination: {
                pageIndex: Math.max((pagination.current_page ?? 1) - 1, 0),
                pageSize: pagination.per_page,
            },
            columnPinning: { left: ['id'], right: ['actions'] },
        },
        state: { sorting, columnVisibility, columnFilters },
        onSortingChange: setSorting,
        onColumnVisibilityChange: setColumnVisibility,
        onColumnFiltersChange,
    });

    // Sync sorting state with server via Inertia
    useEffect(() => {
        const currentSortBy = params.get('sort_by');
        const currentSortDir = params.get('sort_dir');

        const sort = sorting?.[0];
        const desiredSortBy = sort ? String(sort.id) : undefined;
        let desiredSortDir: string | undefined = undefined;

        if (sort) desiredSortDir = sort.desc ? 'desc' : 'asc';

        if (!desiredSortBy && !desiredSortDir && !currentSortBy && !currentSortDir) return;
        if (currentSortBy === desiredSortBy && currentSortDir === desiredSortDir) return;

        onSortingNavigating(desiredSortBy, desiredSortDir);
    }, [sorting, params]);

    // Sync filters state with server via Inertia
    useEffect(() => {
        const currentFilters = params.get('filters');

        const filtersParam = columnFilters?.length
            ? columnFilters.map((f) => `${f.id}:${f.value}`).join(',')
            : undefined;

        if (!currentFilters && !filtersParam) return;
        if (currentFilters === filtersParam) return;

        onFilteringNavigating(filtersParam);
    }, [columnFilters, params]);

    if (!isMounted) return null;

    return (
        <Card className="mx-auto w-full flex-col space-y-4">
            <CardHeader className="flex flex-wrap items-center justify-between gap-4">
                <DataTableToolbar table={table} path={path} />

                <ButtonGroup>
                    {createRoute && (
                        <Button asChild>
                            <Link
                                href={createRoute}
                                aria-label="Create new record of this type"
                                title="Create new record of this type"
                                prefetch
                                as="button"
                            >
                                <ClipboardPlusIcon aria-hidden size={16} />
                                Create
                            </Link>
                        </Button>
                    )}

                    {exportables && (exportables.csvRoute || exportables.pdfRoute) && (
                        <DataTableExportData csv={exportables.csvRoute} pdf={exportables.pdfRoute} />
                    )}
                </ButtonGroup>
            </CardHeader>

            <CardContent className="border-y py-4">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    return (
                                        <TableHead
                                            key={header.id}
                                            colSpan={header.colSpan}
                                            style={{
                                                ...getCommonPinningStyles({ column: header.column }),
                                            }}
                                        >
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(header.column.columnDef.header, header.getContext())}
                                        </TableHead>
                                    );
                                })}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody className="bg-">
                        {table.getRowModel().rows?.length ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow key={row.id} data-state={row.getIsSelected() && 'selected'}>
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell
                                            key={cell.id}
                                            style={{
                                                ...getCommonPinningStyles({ column: cell.column }),
                                            }}
                                        >
                                            {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell colSpan={columns.length} className="h-24 text-center">
                                    No results.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </CardContent>

            <CardFooter className="-mt-2 grid">
                <DataTablePagination pagination={pagination} />
            </CardFooter>
        </Card>
    );
}
