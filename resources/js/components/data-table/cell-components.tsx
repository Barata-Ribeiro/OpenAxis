import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    DropdownMenu,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useCellContext, useTableContext } from '@/hooks/data-table/table';
import { useClipboard } from '@/hooks/use-clipboard';
import { Subscribe } from '@tanstack/react-table';
import { formatDate } from 'date-fns/format';
import { ChevronDown, ChevronRight, Copy, EllipsisVertical, Eye, PencilLine, Trash } from 'lucide-react';

export function SelectCell(): React.ReactNode {
    const cell = useCellContext();
    const table = useTableContext();
    const row = cell.row;

    return (
        <Subscribe source={table.atoms.rowSelection}>
            {() => (
                <Checkbox
                    isSelected={row.getIsSelected()}
                    onChange={(value) => row.toggleSelected(!!value)}
                    aria-label="Select row"
                    className="translate-y-0.5"
                />
            )}
        </Subscribe>
    );
}

export function TextCell(): React.ReactNode {
    const cell = useCellContext<string | undefined>();
    const value = cell.getValue();
    return value == null ? null : <>{String(value)}</>;
}

export function DateCell(): React.ReactNode {
    const cell = useCellContext<string>();
    return <time dateTime={cell.getValue()}>{formatDate(cell.getValue(), 'PPP')}</time>;
}

export function GroupedCell(): React.ReactNode {
    const cell = useCellContext();
    const table = useTableContext();
    const row = cell.row;

    return (
        <Subscribe source={table.atoms.expanded}>
            {() => (
                <Button
                    variant="ghost"
                    size="sm"
                    className="-ml-2 h-7 gap-1 px-2"
                    onClick={row.getToggleExpandedHandler()}
                    isDisabled={!row.getCanExpand()}
                    style={{ paddingLeft: `calc(${row.depth} * 1.5rem + 0.5rem)` }}
                >
                    {row.getIsExpanded() ? <ChevronDown className="size-4" /> : <ChevronRight className="size-4" />}
                    <cell.FlexRender />
                    <span className="text-muted-foreground">({row.subRows.length})</span>
                </Button>
            )}
        </Subscribe>
    );
}

export function JoinDateAggregatedCell(): React.ReactNode {
    const cell = useCellContext<string>();
    const earliest = cell.getValue();
    return <span className="text-muted-foreground">Earliest: {earliest ? formatDate(earliest, 'PPP') : '—'}</span>;
}

// Additional aggregated cells can be added below as needed; e.g. ActionsCell, StatusCell, etc.

// TODO: Implement actions for the ActionsCell dropdown menu items; receive props for links etc...
export function ActionsCell(): React.ReactNode {
    const { copied, copy, error, value } = useClipboard();
    const cell = useCellContext();

    return (
        <DropdownMenuTrigger>
            <Button variant="ghost" className="size-8 p-0">
                <span className="sr-only">Open menu</span>
                <EllipsisVertical aria-hidden className="size-4" />
            </Button>

            <DropdownMenu placement="end">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <DropdownMenuItem>
                    <Copy aria-hidden data-icon="inline-start" />
                    Copy Row
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem>
                    <Eye aria-hidden data-icon="inline-start" /> View
                </DropdownMenuItem>
                <DropdownMenuItem>
                    <PencilLine aria-hidden data-icon="inline-start" /> Edit
                </DropdownMenuItem>
                <DropdownMenuItem variant="destructive">
                    <Trash aria-hidden data-icon="inline-start" />
                    Archive
                </DropdownMenuItem>
            </DropdownMenu>
        </DropdownMenuTrigger>
    );
}
