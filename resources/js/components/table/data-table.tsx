import { ColumnDef, flexRender, getCoreRowModel, SortingState, useReactTable } from '@tanstack/react-table';

import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { buildParams } from '@/lib/utils';
import { PaginationMeta } from '@/types';
import { router } from '@inertiajs/react';
import type { Column, ColumnFiltersState, Updater, VisibilityState } from '@tanstack/react-table';
import { type CSSProperties, useCallback, useEffect, useMemo, useState } from 'react';
import { DataTablePagination } from './data-table-pagination';
import { DataTableToolbar } from './data-table-toolbar';

interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: PaginationMeta<TData[]>['data'];
    pagination: Omit<PaginationMeta<TData[]>, 'data'>;
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

export function DataTable<TData, TValue>({ columns, data, pagination }: Readonly<DataTableProps<TData, TValue>>) {
    const [path] = useState(pagination.path);
    const [params] = useState(new URLSearchParams(globalThis.location.search));

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
        if (!path) return;

        const currentSortBy = params.get('sort_by');
        const currentSortDir = params.get('sort_dir');

        const sort = sorting?.[0];
        const desiredSortBy = sort ? String(sort.id) : undefined;
        let desiredSortDir: string | undefined = undefined;

        if (sort) desiredSortDir = sort.desc ? 'desc' : 'asc';

        if (currentSortBy === desiredSortBy && currentSortDir === desiredSortDir) return;

        router.get(path, buildParams({ sort_by: desiredSortBy, sort_dir: desiredSortDir }), {
            preserveState: true,
            replace: true,
        });
    }, [sorting, path, params]);

    // Sync filters state with server via Inertia
    useEffect(() => {
        if (!path) return;

        const currentFilters = params.get('filters');

        const filtersParam = columnFilters?.length
            ? columnFilters.map((f) => `${f.id}:${f.value}`).join(',')
            : undefined;

        if (currentFilters === filtersParam) return;

        router.get(path, buildParams({ filters: filtersParam }), {
            preserveState: true,
            replace: true,
        });
    }, [columnFilters, params, path]);

    return (
        <Card className="mx-auto w-full flex-col space-y-4">
            <CardHeader>
                <DataTableToolbar table={table} />
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
