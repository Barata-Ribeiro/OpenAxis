'use no memo';

import DataTableColumnVisibility from '@/components/table/data-table-column-visibility';
import { DataTableDateFilter } from '@/components/table/data-table-date-filter';
import { DataTableFacetedFilter } from '@/components/table/data-table-faceted-filter';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { Form, Link } from '@inertiajs/react';
import type { Column, Table } from '@tanstack/react-table';
import { EraserIcon } from 'lucide-react';
import { type ComponentProps, useCallback } from 'react';
import { toast } from 'sonner';

interface DataTableToolbarProps<TData> extends ComponentProps<'div'> {
    table: Table<TData>;
    path: string;
}

export default function DataTableToolbar<TData>({
    table,
    className,
    path,
    ...props
}: Readonly<DataTableToolbarProps<TData>>) {
    const columns = table.getAllColumns().filter((column) => column.getCanFilter());

    return (
        <div
            role="toolbar"
            aria-orientation="horizontal"
            className={cn('flex flex-wrap items-center gap-2 p-1', className)}
            {...props}
        >
            <Form
                action={path}
                method="GET"
                options={{ preserveScroll: true }}
                className="inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
                onError={() => toast.error('Failed to perform search. Please try again.')}
                disableWhileProcessing
            >
                {({ processing, errors }) => (
                    <InputGroup>
                        <InputGroupInput
                            id="search"
                            name="search"
                            placeholder="Type to search..."
                            required
                            aria-required
                            aria-invalid={Boolean(errors.search)}
                        />
                        <InputGroupAddon align="inline-end">
                            {processing ? (
                                <Spinner aria-hidden />
                            ) : (
                                <InputGroupButton type="submit" variant="secondary">
                                    Search
                                </InputGroupButton>
                            )}
                        </InputGroupAddon>
                    </InputGroup>
                )}
            </Form>

            {columns.map((column) => (
                <DataTableToolbarFilter key={column.id} column={column} />
            ))}

            <DataTableColumnVisibility table={table} />

            <Button variant="outline" size="icon" aria-label="Clear filters" title="Clear filters" asChild>
                <Link href={path} as="button" prefetch>
                    <EraserIcon />
                </Link>
            </Button>
        </div>
    );
}

interface DataTableToolbarFilterProps<TData> {
    column: Column<TData>;
}

function DataTableToolbarFilter<TData>({ column }: DataTableToolbarFilterProps<TData>) {
    const columnMeta = column.columnDef.meta;

    const onFilterRender = useCallback(() => {
        if (!columnMeta?.variant) return null;

        switch (columnMeta.variant) {
            case 'text':
                return null; // Text filters will not be implemented in the toolbar

            case 'number':
                return null; // Number filters will not be implemented in the toolbar

            case 'range':
                return null; // Range filters will not be implemented in the toolbar

            case 'date':
            case 'dateRange':
                return (
                    <DataTableDateFilter
                        column={column}
                        title={columnMeta.label ?? column.id}
                        multiple={columnMeta.variant === 'dateRange'}
                    />
                );

            case 'select':
            case 'multiSelect':
                return (
                    <DataTableFacetedFilter
                        column={column}
                        title={columnMeta.label ?? column.id}
                        options={columnMeta.options ?? []}
                        multiple={columnMeta.variant === 'multiSelect'}
                    />
                );

            default:
                return null;
        }
    }, [column, columnMeta]);

    return onFilterRender();
}
