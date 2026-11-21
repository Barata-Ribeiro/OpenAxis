import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { useCellContext, useTableContext } from '@/hooks/data-table/table';
import { Subscribe } from '@tanstack/react-table';
import { formatDate } from 'date-fns/format';
import { ChevronDown, ChevronRight } from 'lucide-react';

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
    return <>{formatDate(cell.getValue(), 'PPP')}</>;
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
