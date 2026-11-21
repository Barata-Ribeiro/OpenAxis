import { reorderItems } from '@/components/data-table/data-table-reorder';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Popover, PopoverTrigger } from '@/components/ui/popover';
import { useTableContext } from '@/hooks/data-table/table';
import { Check, ChevronsUpDown, GripVertical, Settings2 } from 'lucide-react';
import * as React from 'react';
import { GridList, GridListItem, useDragAndDrop, useFilter } from 'react-aria-components';

export function DataTableViewOptions(): React.ReactNode {
    const table = useTableContext();
    const [search, setSearch] = React.useState('');
    const { contains } = useFilter({ sensitivity: 'base' });
    const columns = table.getAllLeafColumns();
    const columnOrder = table.state.columnOrder;
    const orderedColumns = [
        ...columnOrder.map((id) => columns.find((column) => column.id === id)).filter((column) => column !== undefined),
        ...columns.filter((column) => !columnOrder.includes(column.id)),
    ];
    const visibleColumns = orderedColumns.filter(
        (column) => column.accessorFn && contains(column.columnDef.meta?.label ?? column.id, search),
    );
    const { dragAndDropHooks } = useDragAndDrop({
        getItems: (keys) => [...keys].map((key) => ({ 'text/plain': String(key) })),
        onReorder: (event) =>
            table.setColumnOrder(reorderItems(orderedColumns, (column) => column.id, event).map((column) => column.id)),
    });

    return (
        <PopoverTrigger>
            <Button
                aria-label="Toggle columns"
                variant="outline"
                size="sm"
                className="ml-auto hidden h-8 gap-2 lg:flex"
            >
                <Settings2 />
                View
                <ChevronsUpDown className="ml-auto opacity-50" />
            </Button>
            <Popover placement="bottom end" className="w-full max-w-48 p-2">
                <Input
                    aria-label="Search columns"
                    placeholder="Search columns..."
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <GridList
                    aria-label="Columns"
                    keyboardNavigationBehavior="tab"
                    dragAndDropHooks={dragAndDropHooks}
                    renderEmptyState={() => <div className="py-6 text-center text-sm">No columns found.</div>}
                    className="max-h-72 overflow-y-auto [&_.react-aria-DropIndicator[data-drop-target]]:h-1 [&_.react-aria-DropIndicator[data-drop-target]]:bg-primary"
                >
                    {visibleColumns.map((column) => {
                        const label = column.columnDef.meta?.label ?? column.id;
                        return (
                            <GridListItem
                                key={column.id}
                                id={column.id}
                                textValue={label}
                                className="flex items-center gap-1"
                            >
                                <Button
                                    variant="ghost"
                                    className="min-w-0 flex-1 justify-start"
                                    aria-label={`${label} column`}
                                    aria-pressed={column.getIsVisible()}
                                    isDisabled={!column.getCanHide()}
                                    onPress={() => column.toggleVisibility()}
                                >
                                    <Check className={column.getIsVisible() ? 'opacity-100' : 'opacity-0'} />
                                    <span className="truncate">{label}</span>
                                </Button>
                                <Button
                                    slot="drag"
                                    aria-label={`Reorder ${label} column`}
                                    variant="ghost"
                                    size="icon-xs"
                                >
                                    <GripVertical />
                                </Button>
                            </GridListItem>
                        );
                    })}
                </GridList>
                <div className="flex gap-1">
                    <Button
                        variant="outline"
                        size="sm"
                        className="flex-1"
                        onPress={() => table.toggleAllColumnsVisible(false)}
                    >
                        Hide All
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        className="flex-1"
                        onPress={() => table.toggleAllColumnsVisible(true)}
                    >
                        Show All
                    </Button>
                </div>
            </Popover>
        </PopoverTrigger>
    );
}
