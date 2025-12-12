import DataTableColumnHeader from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { InventoryMovementType, inventoryMovementTypeLabel } from '@/types/erp/erp-enums';
import { type StockMovement } from '@/types/erp/product';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns/format';
import { CalendarIcon, CircleDashed } from 'lucide-react';

export const columns: ColumnDef<StockMovement>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'movement_type',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Movement Type" />,
        cell: function Cell({ row }) {
            const rawMovementType = row.original.movement_type;

            return (
                <Badge className="select-none" variant="secondary">
                    {inventoryMovementTypeLabel(rawMovementType)}
                </Badge>
            );
        },
        meta: {
            label: 'Movement Type',
            variant: 'multiSelect',
            options: Object.values(InventoryMovementType).map((type) => ({
                label: inventoryMovementTypeLabel(type),
                value: type,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
        enableHiding: false,
    },
    {
        accessorKey: 'quantity',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Quantity" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.quantity} adjusted product(s)`}
                title={`${row.original.quantity} adjustedproduct(s)`}
            >
                {row.original.quantity}
            </Badge>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'reason',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Reason" />,
        cell: function Cell({ row }) {
            return (
                <Tooltip>
                    <TooltipTrigger asChild>
                        <p className="max-w-[20ch] truncate">{row.original.reason}</p>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>{row.original.reason}</p>
                    </TooltipContent>
                </Tooltip>
            );
        },
        enableSorting: false,
    },
    {
        accessorKey: 'reference',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Reference" />,
        enableSorting: false,
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Created At" />,
        cell: ({ row }) => format(row.original.created_at, 'PPpp'),
        meta: {
            label: 'Created At',
            variant: 'dateRange',
            icon: CalendarIcon,
        },
        enableSorting: true,
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Updated At" />,
        cell: ({ row }) => format(row.original.updated_at, 'PPpp'),
        enableSorting: true,
    },
];
