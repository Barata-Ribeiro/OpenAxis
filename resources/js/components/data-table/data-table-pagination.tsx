import { useTableContext } from '@/hooks/data-table/table';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-react';
import * as React from 'react';

import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

export function DataTablePagination({
    pageSizeOptions = [10, 20, 30, 40, 50, Infinity],
}: Readonly<{
    pageSizeOptions?: Array<number>;
}>): React.ReactNode {
    const table = useTableContext();

    return (
        <div className="flex w-full flex-col-reverse items-center justify-between gap-4 overflow-auto p-1 sm:flex-row sm:gap-8">
            <div className="flex-1 text-sm whitespace-nowrap text-muted-foreground">
                {table.getFilteredSelectedRowModel().rows.length.toLocaleString()} of{' '}
                {table.getFilteredRowModel().rows.length.toLocaleString()} row(s) selected.
            </div>
            <div className="flex flex-col-reverse items-center gap-4 sm:flex-row sm:gap-6 lg:gap-8">
                <div className="flex items-center space-x-2">
                    <p className="text-sm font-medium whitespace-nowrap">Rows per page</p>
                    <Select
                        aria-label="Rows per page"
                        selectedKey={`${table.state.pagination.pageSize}`}
                        onSelectionChange={(value) => {
                            if (value !== null) table.setPageSize(Number(value));
                        }}
                    >
                        <SelectTrigger className="h-8 w-18">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent placement="top">
                            <SelectGroup>
                                {pageSizeOptions.map((pageSize) => (
                                    <SelectItem key={pageSize} id={`${pageSize}`}>
                                        {pageSize === Infinity ? 'All' : String(pageSize)}
                                    </SelectItem>
                                ))}
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div className="flex items-center justify-center text-sm font-medium">
                    Page {(table.state.pagination.pageIndex + 1).toLocaleString()} of{' '}
                    {table.getPageCount().toLocaleString()}
                </div>
                <div className="flex items-center space-x-2">
                    <Button
                        aria-label="Go to first page"
                        variant="outline"
                        className="hidden size-8 p-0 lg:flex"
                        onClick={() => table.setPageIndex(0)}
                        isDisabled={!table.getCanPreviousPage()}
                    >
                        <ChevronsLeft className="size-4" aria-hidden />
                    </Button>
                    <Button
                        aria-label="Go to previous page"
                        variant="outline"
                        size="icon"
                        className="size-8"
                        onClick={() => table.previousPage()}
                        isDisabled={!table.getCanPreviousPage()}
                    >
                        <ChevronLeft className="size-4" aria-hidden />
                    </Button>
                    <Button
                        aria-label="Go to next page"
                        variant="outline"
                        size="icon"
                        className="size-8"
                        onClick={() => table.nextPage()}
                        isDisabled={!table.getCanNextPage()}
                    >
                        <ChevronRight className="size-4" aria-hidden />
                    </Button>
                    <Button
                        aria-label="Go to last page"
                        variant="outline"
                        size="icon"
                        className="hidden size-8 lg:flex"
                        onClick={() => table.lastPage()}
                        isDisabled={!table.getCanLastPage()}
                    >
                        <ChevronsRight className="size-4" aria-hidden />
                    </Button>
                </div>
            </div>
        </div>
    );
}
