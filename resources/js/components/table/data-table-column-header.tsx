'use no memo';

import { Column } from '@tanstack/react-table';
import { ArrowDown, ArrowUp, ChevronsUpDown, EyeOffIcon } from 'lucide-react';

import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';
import { Activity, ComponentProps } from 'react';

interface DataTableColumnHeaderProps<TData, TValue> extends ComponentProps<typeof DropdownMenuTrigger> {
    column: Column<TData, TValue>;
    title: string;
}

export function DataTableColumnHeader<TData, TValue>({
    column,
    title,
    className,
    ...props
}: Readonly<DataTableColumnHeaderProps<TData, TValue>>) {
    if (!column.getCanSort() && !column.getCanHide()) {
        return <div className={cn(className)}>{title}</div>;
    }

    const columnSortDir = column.getIsSorted();

    const columnSortIndicator = {
        asc: <ArrowUp aria-hidden />,
        desc: <ArrowDown aria-hidden />,
        default: <ChevronsUpDown aria-hidden />,
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger
                className={cn(
                    '-ml-1.5 flex h-8 items-center gap-1.5 rounded-md px-2 py-1.5 hover:bg-accent focus:ring-1 focus:ring-ring focus:outline-none data-[state=open]:bg-accent [&_svg]:size-4 [&_svg]:shrink-0 [&_svg]:text-muted-foreground',
                    className,
                )}
                {...props}
            >
                <span>{title}</span>
                {columnSortDir ? columnSortIndicator[columnSortDir] : columnSortIndicator['default']}
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start" className="w-28">
                <Activity mode={column.getCanSort() ? 'visible' : 'hidden'}>
                    <DropdownMenuCheckboxItem
                        className="relative pr-8 pl-2 [&_svg]:text-muted-foreground [&>span:first-child]:right-2 [&>span:first-child]:left-auto"
                        checked={column.getIsSorted() === 'asc'}
                        onClick={() => column.toggleSorting(false)}
                    >
                        {columnSortIndicator['asc']}
                        Asc
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem
                        className="relative pr-8 pl-2 [&_svg]:text-muted-foreground [&>span:first-child]:right-2 [&>span:first-child]:left-auto"
                        checked={column.getIsSorted() === 'desc'}
                        onClick={() => column.toggleSorting(true)}
                    >
                        {columnSortIndicator['desc']}
                        Desc
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuItem
                        className="pl-2 [&_svg]:text-muted-foreground"
                        onClick={() => column.clearSorting()}
                    >
                        {columnSortIndicator['default']}
                        Clear
                    </DropdownMenuItem>
                </Activity>

                <Activity mode={column.getCanHide() && column.getCanSort() ? 'visible' : 'hidden'}>
                    <DropdownMenuSeparator />
                </Activity>

                <Activity mode={column.getCanHide() ? 'visible' : 'hidden'}>
                    <DropdownMenuCheckboxItem
                        className="relative pr-8 pl-2 [&_svg]:text-muted-foreground [&>span:first-child]:right-2 [&>span:first-child]:left-auto"
                        checked={!column.getIsVisible()}
                        onClick={() => column.toggleVisibility(false)}
                    >
                        <EyeOffIcon aria-hidden />
                        Hide
                    </DropdownMenuCheckboxItem>
                </Activity>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
