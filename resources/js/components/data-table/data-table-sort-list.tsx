import { reorderItems } from '@/components/data-table/data-table-reorder';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useTableContext } from '@/hooks/data-table/table';
import { cn } from '@/lib/utils';
import { ArrowDownUp, Check, ChevronsUpDown, GripVertical, Trash2 } from 'lucide-react';
import * as React from 'react';
import { GridList, GridListItem, useDragAndDrop } from 'react-aria-components';

export function DataTableSortList(): React.ReactNode {
    const table = useTableContext();
    const sorting = table.state.sorting;
    const sortableColumns = table.getAllColumns().filter((column) => column.getCanSort());
    const labelId = React.useId();
    const descriptionId = React.useId();
    const { dragAndDropHooks } = useDragAndDrop({
        getItems: (keys) => [...keys].map((key) => ({ 'text/plain': String(key) })),
        onReorder: (event) => table.setSorting(reorderItems(sorting, (item) => item.id, event)),
    });

    return (
        <PopoverTrigger>
            <Button variant="outline" size="sm">
                <ArrowDownUp />
                Sort
                {sorting.length > 0 && <Badge variant="secondary">{sorting.length}</Badge>}
            </Button>
            <Popover
                placement="bottom start"
                containerPadding={16}
                aria-labelledby={labelId}
                aria-describedby={descriptionId}
                className="flex w-[calc(100vw-(--spacing(20)))] max-w-[25rem] min-w-72 flex-col gap-3 p-4 sm:w-[25rem]"
            >
                <div className="flex flex-col gap-1">
                    <h4 id={labelId} className="leading-none font-medium">
                        {sorting.length > 0 ? 'Sort by' : 'No sorting applied'}
                    </h4>
                    <p
                        id={descriptionId}
                        className={cn('text-sm text-muted-foreground', sorting.length > 0 && 'sr-only')}
                    >
                        {sorting.length > 0
                            ? 'Modify sorting to organize your results.'
                            : 'Add sorting to organize your results.'}
                    </p>
                </div>
                {sorting.length > 0 && (
                    <GridList
                        aria-label="Sorting priority"
                        keyboardNavigationBehavior="tab"
                        dragAndDropHooks={dragAndDropHooks}
                        className="flex max-h-[300px] flex-col gap-2 overflow-y-auto p-0.5 [&_.react-aria-DropIndicator[data-drop-target]]:h-1 [&_.react-aria-DropIndicator[data-drop-target]]:bg-primary"
                    >
                        {sorting.map((sort) => {
                            const title =
                                sortableColumns.find((column) => column.id === sort.id)?.columnDef.meta?.label ??
                                sort.id;
                            return (
                                <GridListItem
                                    key={sort.id}
                                    id={sort.id}
                                    textValue={title}
                                    className="grid grid-cols-[175px_100px_32px_32px] items-center gap-2"
                                >
                                    <PopoverTrigger>
                                        <Button
                                            aria-label={`Select column to sort by. Current: ${title}`}
                                            variant="outline"
                                            size="sm"
                                            className="h-8 justify-between font-normal"
                                        >
                                            <span className="truncate">{title}</span>
                                            <ChevronsUpDown className="opacity-50" />
                                        </Button>
                                        <Popover className="w-(--trigger-width) p-0">
                                            <Command>
                                                <CommandInput
                                                    placeholder="Search columns..."
                                                    aria-label="Search sortable columns"
                                                />
                                                <CommandList
                                                    renderEmptyState={() => (
                                                        <CommandEmpty>No column found.</CommandEmpty>
                                                    )}
                                                >
                                                    <CommandGroup>
                                                        {sortableColumns
                                                            .filter(
                                                                (column) =>
                                                                    !sorting.some(
                                                                        (item) =>
                                                                            item.id === column.id &&
                                                                            item.id !== sort.id,
                                                                    ),
                                                            )
                                                            .map((column) => (
                                                                <CommandItem
                                                                    key={column.id}
                                                                    id={column.id}
                                                                    textValue={
                                                                        column.columnDef.meta?.label ?? column.id
                                                                    }
                                                                    onAction={() =>
                                                                        table.setSorting(
                                                                            sorting.map((item) =>
                                                                                item.id === sort.id
                                                                                    ? { ...item, id: column.id }
                                                                                    : item,
                                                                            ),
                                                                        )
                                                                    }
                                                                >
                                                                    <span className="truncate">
                                                                        {column.columnDef.meta?.label ?? column.id}
                                                                    </span>
                                                                    <Check
                                                                        className={cn(
                                                                            'ml-auto size-4',
                                                                            column.id === sort.id
                                                                                ? 'opacity-100'
                                                                                : 'opacity-0',
                                                                        )}
                                                                        aria-hidden="true"
                                                                    />
                                                                </CommandItem>
                                                            ))}
                                                    </CommandGroup>
                                                </CommandList>
                                            </Command>
                                        </Popover>
                                    </PopoverTrigger>
                                    <Select
                                        aria-label={`Sort direction for ${title}`}
                                        selectedKey={sort.desc ? 'desc' : 'asc'}
                                        onSelectionChange={(value) => {
                                            if (value === 'asc' || value === 'desc') {
                                                table.setSorting(
                                                    sorting.map((item) =>
                                                        item.id === sort.id
                                                            ? { ...item, desc: value === 'desc' }
                                                            : item,
                                                    ),
                                                );
                                            }
                                        }}
                                    >
                                        <SelectTrigger className="h-8">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem id="asc">Asc</SelectItem>
                                                <SelectItem id="desc">Desc</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <Button
                                        aria-label={`Remove sort for ${title}`}
                                        variant="outline"
                                        size="icon"
                                        onPress={() => table.setSorting(sorting.filter((item) => item.id !== sort.id))}
                                    >
                                        <Trash2 />
                                    </Button>
                                    <Button
                                        slot="drag"
                                        aria-label={`Reorder ${title} sort`}
                                        variant="outline"
                                        size="icon"
                                    >
                                        <GripVertical />
                                    </Button>
                                </GridListItem>
                            );
                        })}
                    </GridList>
                )}
                <div className="flex items-center gap-2">
                    <Button
                        aria-label="Add new sort"
                        size="sm"
                        isDisabled={sorting.length >= sortableColumns.length}
                        onPress={() => {
                            const column = sortableColumns.find((item) => !sorting.some((sort) => sort.id === item.id));
                            if (column) table.setSorting([...sorting, { id: column.id, desc: false }]);
                        }}
                    >
                        Add sort
                    </Button>
                    {sorting.length > 0 && (
                        <Button
                            aria-label="Reset all sorting"
                            size="sm"
                            variant="outline"
                            onPress={() => table.resetSorting()}
                        >
                            Reset
                        </Button>
                    )}
                </div>
            </Popover>
        </PopoverTrigger>
    );
}
