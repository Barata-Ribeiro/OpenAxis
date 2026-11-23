import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { features } from '@/hooks/data-table/features';
import { useAppTable } from '@/hooks/data-table/table';
import { useDebouncedCallback } from '@/hooks/use-debounce-callback';
import type { Column, ColumnDef, RowData, Updater } from '@tanstack/react-table';
import { useEffect, useState } from 'react';

type Props<TData extends { id: string | number }> = {
    columns: ColumnDef<typeof features, TData>[];
    data: TData[];
};

function getCommonPinningStyles<TData extends RowData>(
    column: Column<typeof features, TData>,
    isSelected = false,
): React.CSSProperties {
    const isPinned = column.getIsPinned();
    const isLastLeftPinnedColumn = isPinned === 'start' && column.getIsLastColumn('start');
    const isFirstRightPinnedColumn = isPinned === 'end' && column.getIsFirstColumn('end');

    const rightPinnedColumnShadow = isFirstRightPinnedColumn ? '4px 0 4px -4px var(--border) inset' : undefined;
    const pinnedBackgroundColor = isPinned ? 'var(--background)' : undefined;
    return {
        boxShadow: isLastLeftPinnedColumn ? '-4px 0 4px -4px var(--border) inset' : rightPinnedColumnShadow,
        insetInlineStart: isPinned === 'start' ? `${column.getStart('start')}px` : undefined,
        insetInlineEnd: isPinned === 'end' ? `${column.getAfter('end')}px` : undefined,
        position: isPinned ? 'sticky' : 'relative',
        background: isSelected ? 'var(--muted)' : pinnedBackgroundColor,
        zIndex: isPinned ? 1 : 0,
    };
}

function update<T>(updater: Updater<T>, previous: T): T {
    return typeof updater === 'function' ? (updater as (value: T) => T)(previous) : updater;
}

function DebouncedInput({
    value: initialValue,
    onChange,
    debounce = 300,
    ...props
}: {
    value: string | number;
    onChange: (value: string | number) => void;
    debounce?: number;
} & Omit<React.ComponentProps<typeof Input>, 'onChange'>) {
    const [value, setValue] = useState(initialValue);

    useEffect(() => setValue(initialValue), [initialValue]);

    const debouncedOnChange = useDebouncedCallback(onChange, { delay: debounce });

    return (
        <Input
            {...props}
            value={value}
            onChange={(e) => {
                setValue(e.target.value);
                debouncedOnChange(e.target.value);
            }}
        />
    );
}

export default function DataTable<TData extends { id: string | number }>({ columns, data }: Readonly<Props<TData>>) {
    const table = useAppTable({
        key: 'id',
        columns,
        data,
    });

    return (
        <table.AppTable>
            <Card className="mx-auto w-full">
                <CardHeader className="flex flex-wrap items-center justify-between gap-4">
                    {/* TODO: Implement search input; filters; sorting; view options; reset table */}
                    <table.FilterList />
                    <table.SortList />
                    <table.ViewOptions />
                </CardHeader>

                <CardContent className="border-y py-4">{/* TODO: Implement table here */}</CardContent>

                <CardFooter>
                    <table.Pagination />
                </CardFooter>
            </Card>
        </table.AppTable>
    );
}
