'use no memo';

import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';
import { cn } from '@/lib/utils';
import administrative from '@/routes/administrative';
import { Form, Link } from '@inertiajs/react';
import { Column, Table } from '@tanstack/react-table';
import { EraserIcon } from 'lucide-react';
import { type ComponentProps, useCallback } from 'react';
import { toast } from 'sonner';
import { Button } from '../ui/button';
import { Spinner } from '../ui/spinner';
import DataTableColumnVisibility from './data-table-column-visibility';
import { DataTableFacetedFilter } from './data-table-faceted-filter';

interface DataTableToolbarProps<TData> extends ComponentProps<'div'> {
    table: Table<TData>;
}

export function DataTableToolbar<TData>({ table, className, ...props }: Readonly<DataTableToolbarProps<TData>>) {
    const columns = table.getAllColumns().filter((column) => column.getCanFilter());

    return (
        <div
            role="toolbar"
            aria-orientation="horizontal"
            className={cn('flex flex-wrap items-center gap-2 p-1', className)}
            {...props}
        >
            <Form
                {...administrative.users.index.form()}
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
                <Link href={administrative.users.index()} as="button" prefetch>
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
                // return (
                //     <Input
                //         type="search"
                //         placeholder={columnMeta.placeholder ?? columnMeta.label}
                //         value={(column.getFilterValue() as string) ?? ''}
                //         onChange={(event) => column.setFilterValue(event.target.value)}
                //         className="h-8 w-40 lg:w-56"
                //     />
                // );
                return null; // Text filters will not be implemented in the toolbar

            case 'number':
                // return (
                //     <div className="relative">
                //         <Input
                //             type="number"
                //             inputMode="numeric"
                //             placeholder={columnMeta.placeholder ?? columnMeta.label}
                //             value={(column.getFilterValue() as string) ?? ''}
                //             onChange={(event) => column.setFilterValue(event.target.value)}
                //             className={cn('h-8 w-[120px]', columnMeta.unit && 'pr-8')}
                //         />
                //         {columnMeta.unit && (
                //             <span className="absolute top-0 right-0 bottom-0 flex items-center rounded-r-md bg-accent px-2 text-sm text-muted-foreground">
                //                 {columnMeta.unit}
                //             </span>
                //         )}
                //     </div>
                // );
                return null; // Number filters will not be implemented in the toolbar

            case 'range':
                return null; // Range filters will not be implemented in the toolbar

            case 'date':
            case 'dateRange':
                return null; // TODO: Implement date and date range filters in the toolbar

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
