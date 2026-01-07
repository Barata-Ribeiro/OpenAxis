import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import ActionConfirmationDialog from '@/components/feedback/action-confirmation-dialog';
import DataTableColumnHeader from '@/components/table/data-table-column-header';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useInitials } from '@/hooks/use-initials';
import { usePermission } from '@/hooks/use-permission';
import { formatCurrency } from '@/lib/utils';
import erp from '@/routes/erp';
import { PurchaseOrderStatus, purchaseOrderStatusLabel } from '@/types/erp/erp-enums';
import type { PurchaseOrderWithRelations } from '@/types/erp/purchase-order';
import { Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed, DeleteIcon, Ellipsis, EyeIcon } from 'lucide-react';
import { useState } from 'react';

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
    {
        id: 'actions',
        cell: function Cell({ row }) {
            const [open, setOpen] = useState(false);
            const { can } = usePermission();

            const purchaseOrder = row.original;

            const supplierNameToCopy = purchaseOrder.supplier.name;

            return (
                <>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                aria-label="Open menu"
                                variant="ghost"
                                className="flex size-8 p-0 data-[state=open]:bg-muted"
                            >
                                <Ellipsis aria-hidden size={16} />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" className="w-40">
                            <DropdownMenuLabel>Copy Fields</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem asChild>
                                    <DropdownMenuCopyButton content={supplierNameToCopy}>
                                        Copy Supplier
                                    </DropdownMenuCopyButton>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem disabled={!can('order.edit')} asChild>
                                    <Link
                                        className="block w-full"
                                        href={erp.purchaseOrders.edit(purchaseOrder.id)}
                                        as="button"
                                    >
                                        <EyeIcon aria-hidden size={14} /> Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    variant="destructive"
                                    disabled={!can('order.destroy')}
                                    onSelect={() => setOpen(true)}
                                >
                                    <DeleteIcon aria-hidden size={14} /> Delete
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <ActionConfirmationDialog
                        open={open}
                        setOpen={setOpen}
                        title="Confirm Deletion"
                        description={`Are you sure you want to delete the purchase order "${purchaseOrder.id}"? This action cannot be undone.`}
                        method="delete"
                        route={erp.purchaseOrders.destroy(purchaseOrder.id)}
                    />
                </>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
