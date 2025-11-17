import { ColumnDef, flexRender, getCoreRowModel, SortingState, useReactTable } from '@tanstack/react-table';

import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { buildParams } from '@/lib/utils';
import { PaginationMeta } from '@/types';
import { router } from '@inertiajs/react';
import { useLayoutEffect, useState } from 'react';

interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: PaginationMeta<TData[]>['data'];
    pagination: Omit<PaginationMeta<TData[]>, 'data'>;
}

export function DataTable<TData, TValue>({ columns, data, pagination }: Readonly<DataTableProps<TData, TValue>>) {
    const [sorting, setSorting] = useState<SortingState>(() => {
        const params = new URLSearchParams(globalThis.location.search);
        const sort_by = params.get('sort_by');
        const sort_dir = params.get('sort_dir');
        if (!sort_by) return [];

        return [{ id: sort_by, desc: sort_dir === 'desc' }];
    });

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
        },
        state: {
            sorting,
        },
        onSortingChange: setSorting,
    });

    // Sync sorting state with server via Inertia
    useLayoutEffect(() => {
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
        <div className="overflow-hidden rounded-md border">
            {/* TODO: Add search and date filters */}
            <Table>
                <TableHeader>
                    {table.getHeaderGroups().map((headerGroup) => (
                        <TableRow key={headerGroup.id}>
                            {headerGroup.headers.map((header) => {
                                return (
                                    <TableHead key={header.id}>
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
                                    <TableCell key={cell.id}>
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

            {/* TODO: Add pagination controls */}
        </div>
    );
}
