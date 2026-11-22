import { Checkbox } from '@/components/ui/checkbox';
import { useHeaderContext, useTableContext } from '@/hooks/data-table/table';
import { cn } from '@/lib/utils';
import { Subscribe } from '@tanstack/react-table';

export function SelectAllHeader(): React.ReactNode {
    const table = useTableContext();

    return (
        <Subscribe source={table.atoms.rowSelection}>
            {() => (
                <Checkbox
                    isSelected={table.getIsAllPageRowsSelected()}
                    isIndeterminate={table.getIsSomePageRowsSelected() && !table.getIsAllPageRowsSelected()}
                    onChange={(value) => table.toggleAllPageRowsSelected(!!value)}
                    aria-label="Select all"
                    className="translate-y-0.5"
                />
            )}
        </Subscribe>
    );
}

export function ResizeHandle(): React.ReactNode {
    const header = useHeaderContext();
    const table = useTableContext();

    if (!header.column.getCanResize()) return null;

    return (
        <Subscribe source={table.atoms.columnResizing}>
            {() => (
                <div
                    onDoubleClick={() => header.column.resetSize()}
                    onMouseDown={header.getResizeHandler()}
                    onTouchStart={header.getResizeHandler()}
                    className={cn(
                        'absolute top-1/2 -right-0.5 z-10 h-6 w-0.75 -translate-y-1/2 cursor-e-resize touch-none rounded-md transition-colors select-none before:absolute before:-inset-x-1 before:top-0 before:h-full hover:bg-blue-600',
                        header.column.getIsResizing() && 'bg-blue-600',
                    )}
                />
            )}
        </Subscribe>
    );
}
