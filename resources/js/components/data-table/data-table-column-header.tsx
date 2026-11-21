import { useHeaderContext, useTableContext } from '@/hooks/data-table/table';
import { Subscribe } from '@tanstack/react-table';
import { ArrowDown, ArrowUp, ChevronsUpDown, EyeOff, Group, Pin, PinOff, Ungroup } from 'lucide-react';

import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';
import { Activity } from 'react';

export function ColumnHeader({ title, className }: Readonly<{ title?: string; className?: string }>): React.ReactNode {
    const header = useHeaderContext();
    const table = useTableContext();
    const column = header.column;

    const displayTitle = column.columnDef.meta?.label ?? title ?? column.id;

    const canSort = column.getCanSort();
    const canHide = column.getCanHide();
    const canPin = column.getCanPin();
    const canGroup = column.getCanGroup();

    if (!canSort && !canHide && !canPin && !canGroup) {
        return <div className={cn(className)}>{displayTitle}</div>;
    }

    return (
        <div className={cn('flex items-center gap-2', className)}>
            <Subscribe
                source={table.store}
                selector={(s) => ({
                    sorting: s.sorting,
                    grouping: s.grouping,
                    columnPinning: s.columnPinning,
                })}
            >
                {() => {
                    const sorted = canSort ? column.getIsSorted() : false;
                    const pinned = canPin ? column.getIsPinned() : false;
                    const grouped = canGroup ? column.getIsGrouped() : false;

                    const sortIcons: Record<'asc' | 'desc' | 'none', React.ReactNode> = {
                        asc: <ArrowUp className="ml-2 size-4" />,
                        desc: <ArrowDown className="ml-2 size-4" />,
                        none: canSort && (
                            <ChevronsUpDown className="ml-2 size-4 opacity-0 transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100 group-data-[state=open]:opacity-100" />
                        ),
                    };

                    const sortIcon = sortIcons[sorted || 'none'];

                    return (
                        <DropdownMenuTrigger>
                            <Button variant="ghost" size="sm" className="group -ml-3 h-8 data-[state=open]:bg-accent">
                                <span>{displayTitle}</span>
                                {sortIcon}
                            </Button>

                            <DropdownMenu placement="start">
                                <Activity mode={canSort ? 'visible' : 'hidden'}>
                                    <DropdownMenuItem onClick={() => column.toggleSorting(false)}>
                                        <ArrowUp className="mr-2 size-3.5 text-muted-foreground/70" />
                                        Asc
                                    </DropdownMenuItem>
                                    <DropdownMenuItem onClick={() => column.toggleSorting(true)}>
                                        <ArrowDown className="mr-2 size-3.5 text-muted-foreground/70" />
                                        Desc
                                    </DropdownMenuItem>
                                </Activity>
                                <Activity mode={canGroup ? 'visible' : 'hidden'}>
                                    {canSort ? <DropdownMenuSeparator /> : null}
                                    <DropdownMenuItem onClick={column.getToggleGroupingHandler()}>
                                        {grouped ? (
                                            <>
                                                <Ungroup className="mr-2 size-3.5 text-muted-foreground/70" />
                                                Ungroup
                                            </>
                                        ) : (
                                            <>
                                                <Group className="mr-2 size-3.5 text-muted-foreground/70" />
                                                Group by
                                            </>
                                        )}
                                    </DropdownMenuItem>
                                </Activity>
                                <Activity mode={canPin ? 'visible' : 'hidden'}>
                                    {canSort || canGroup ? <DropdownMenuSeparator /> : null}
                                    <DropdownMenuItem
                                        onClick={() => column.pin('start')}
                                        isDisabled={pinned === 'start'}
                                    >
                                        <Pin className="mr-2 size-3.5 text-muted-foreground/70" />
                                        Pin left
                                    </DropdownMenuItem>
                                    <DropdownMenuItem onClick={() => column.pin('end')} isDisabled={pinned === 'end'}>
                                        <Pin className="mr-2 size-3.5 rotate-180 text-muted-foreground/70" />
                                        Pin right
                                    </DropdownMenuItem>
                                    {pinned ? (
                                        <DropdownMenuItem onClick={() => column.pin(false)}>
                                            <PinOff className="mr-2 size-3.5 text-muted-foreground/70" />
                                            Unpin
                                        </DropdownMenuItem>
                                    ) : null}
                                </Activity>

                                <Activity mode={canHide ? 'visible' : 'hidden'}>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem onClick={() => column.toggleVisibility(false)}>
                                        <EyeOff className="mr-2 size-3.5 text-muted-foreground/70" />
                                        Hide
                                    </DropdownMenuItem>
                                </Activity>
                            </DropdownMenu>
                        </DropdownMenuTrigger>
                    );
                }}
            </Subscribe>
        </div>
    );
}
