import DataTableColumnHeader from '@/components/table/data-table-column-header';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useInitials } from '@/hooks/use-initials';
import { formatCurrency } from '@/lib/utils';
import { PurchaseOrderStatus, purchaseOrderStatusLabel } from '@/types/erp/erp-enums';
import type { PurchaseOrderWithRelations } from '@/types/erp/purchase-order';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed } from 'lucide-react';

export const columns: ColumnDef<PurchaseOrderWithRelations>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'supplier.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Supplier" />,
        cell: function Cell({ row }) {
            const purchaseOrder = row.original;

            return (
                <div className="flex flex-col">
                    <span className="font-medium">{purchaseOrder.supplier.name}</span>
                    <span className="text-xs text-muted-foreground">{purchaseOrder.supplier.email}</span>
                </div>
            );
        },
        enableSorting: true,
    },
    {
        accessorKey: 'total_cost',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Total Cost" />,
        cell: ({ row }) => formatCurrency(row.original.total_cost),
        enableSorting: true,
    },
    {
        accessorKey: 'status',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        cell: function Cell({ row }) {
            const status = row.original.status;
            const variant = status === PurchaseOrderStatus.CANCELED ? 'destructive' : 'outline';
            const label = `Purchase order status is '${purchaseOrderStatusLabel(status)}'`;

            return (
                <Badge
                    className="h-5 min-w-5 px-2 font-mono tabular-nums"
                    variant={variant}
                    aria-label={label}
                    title={label}
                >
                    {purchaseOrderStatusLabel(status)}
                </Badge>
            );
        },
        meta: {
            label: 'Purchase Order Status',
            variant: 'multiSelect',
            options: Object.values(PurchaseOrderStatus).map((status) => ({
                label: purchaseOrderStatusLabel(status),
                value: status,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
    },
    {
        accessorKey: 'user.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Purchaser" />,
        cell: function Cell({ row }) {
            const getInitials = useInitials();
            const createdByUser = row.original.user;

            return (
                <div className="inline-flex items-center gap-x-2">
                    <Avatar className="size-6 overflow-hidden rounded-full">
                        <AvatarImage
                            src={createdByUser.avatar.src ?? ''}
                            srcSet={createdByUser.avatar.srcSet ?? ''}
                            alt={createdByUser.name}
                            className="object-cover"
                        />
                        <AvatarFallback className="rounded-lg bg-neutral-200 text-xs text-black select-none dark:bg-neutral-700 dark:text-white">
                            {getInitials(createdByUser.name)}
                        </AvatarFallback>
                    </Avatar>
                    <div className="flex flex-col">
                        <p className="truncate font-medium">{createdByUser.name}</p>
                        <p className="truncate text-xs text-muted-foreground">{createdByUser.email}</p>
                    </div>
                </div>
            );
        },
        enableSorting: true,
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

    //TODO: Add actions column
];
