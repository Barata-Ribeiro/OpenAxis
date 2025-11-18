import { ColumnDef, flexRender, getCoreRowModel, SortingState, useReactTable } from '@tanstack/react-table';

import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { buildParams } from '@/lib/utils';
import { PaginationMeta } from '@/types';
import { router } from '@inertiajs/react';
import type { Column, VisibilityState } from '@tanstack/react-table';
import { useEffect, useState } from 'react';
import DataTableColumnVisibility from './data-table-column-visibility';
import { DataTablePagination } from './data-table-pagination';

interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: PaginationMeta<TData[]>['data'];
    pagination: Omit<PaginationMeta<TData[]>, 'data'>;
}

function getCommonPinningStyles<TData>({
    column,
    withBorder = false,
}: {
    column: Column<TData>;
    withBorder?: boolean;
}): React.CSSProperties {
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
    const [sorting, setSorting] = useState<SortingState>(() => {
        const params = new URLSearchParams(globalThis.location.search);
        const sort_by = params.get('sort_by');
        const sort_dir = params.get('sort_dir');
        if (!sort_by) return [];

        return [{ id: sort_by, desc: sort_dir === 'desc' }];
    });

    const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});

    const table = useReactTable({
        columns,
        data,
        getCoreRowModel: getCoreRowModel(),
        manualPagination: true, // turn off client-side pagination
        manualSorting: true, // turn off client-side sorting
        pageCount: pagination.last_page ?? Math.ceil((pagination.total ?? 0) / (pagination.per_page ?? 1)),
        initialState: {
            pagination: {
                pageIndex: Math.max((pagination.current_page ?? 1) - 1, 0),
                pageSize: pagination.per_page,
            },
            columnPinning: { left: ['id'], right: ['actions'] },
        },
        onSortingChange: setSorting,
        onColumnVisibilityChange: setColumnVisibility,
        state: { sorting, columnVisibility },
    });

    // Sync sorting state with server via Inertia
    useEffect(() => {
        if (!pagination.path) return;

        const params = new URLSearchParams(globalThis.location.search);
        const currentSortBy = params.get('sort_by');
        const currentSortDir = params.get('sort_dir');

        const sort = sorting?.[0];
        const desiredSortBy = sort ? String(sort.id) : undefined;
        let desiredSortDir: string | undefined = undefined;

        if (sort) desiredSortDir = sort.desc ? 'desc' : 'asc';

        if (currentSortBy === desiredSortBy && currentSortDir === desiredSortDir) return;

        router.get(pagination.path, buildParams({ sort_by: desiredSortBy, sort_dir: desiredSortDir }), {
            preserveState: true,
            replace: true,
        });
    }, [sorting, pagination.path]);

    return (
        <Card className="mx-auto w-full flex-col space-y-4">
            <CardHeader>
                <DataTableColumnVisibility table={table} />
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
