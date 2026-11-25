import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    DropdownMenu,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useCellContext, useTableContext } from '@/hooks/data-table/table';
import { useClipboard } from '@/hooks/use-clipboard';
import { usePermission } from '@/hooks/use-permission';
import { renderInertiaLink } from '@/lib/inertia-link';
import type { Permission } from '@/types/role-permission';
import { useModalStack } from '@inertiaui/modal-react';
import { Subscribe } from '@tanstack/react-table';
import { formatDate } from 'date-fns/format';
import { ChevronDown, ChevronRight, Copy, EllipsisVertical, type LucideIcon } from 'lucide-react';
import { toast } from 'sonner';

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

export type RowAction = {
    label: string;
    href: string;
    icon: LucideIcon;
    permissions?: readonly Permission['name'][];
    destructive?: boolean;
};

export function ActionsCell({
    actions,
    modal = false,
}: Readonly<{ actions: readonly (RowAction | false)[]; modal?: boolean }>): React.ReactNode {
    const { copy } = useClipboard();
    const { can } = usePermission();
    const { visitModal } = useModalStack();
    const cell = useCellContext();
    const availableActions = actions.filter(
        (action): action is RowAction =>
            action !== false && (!action.permissions || action.permissions.every((permission) => can(permission))),
    );

    if (availableActions.length === 0) return null;

    async function copyRow() {
        const text = cell.row
            .getVisibleCells()
            .filter(({ column }) => column.accessorFn)
            .map((valueCell) => {
                const value = valueCell.getValue();
                return value == null || typeof value === 'string' ? value : JSON.stringify(value);
            })
            .join('\t');

        if (await copy(text)) {
            toast.success('Row copied');
        } else {
            toast.error('Could not copy row');
        }
    }

    return (
        <DropdownMenuTrigger>
            <Button variant="ghost" size="icon-sm" aria-label="Row actions">
                <EllipsisVertical aria-hidden />
            </Button>

            <DropdownMenu placement="bottom end">
                <DropdownMenuItem textValue="Copy row" onAction={() => void copyRow()}>
                    <Copy aria-hidden data-icon="inline-start" />
                    Copy row
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                {availableActions.map(({ href, label, icon: Icon, destructive }) => (
                    <DropdownMenuItem
                        key={label}
                        textValue={label}
                        variant={destructive ? 'destructive' : 'default'}
                        {...(modal
                            ? {
                                  onAction: () =>
                                      void visitModal(href, { navigate: true }).catch(() =>
                                          toast.error('Could not open action'),
                                      ),
                              }
                            : { href, render: renderInertiaLink(href) })}
                    >
                        <Icon aria-hidden data-icon="inline-start" />
                        {label}
                    </DropdownMenuItem>
                ))}
            </DropdownMenu>
        </DropdownMenuTrigger>
    );
}
