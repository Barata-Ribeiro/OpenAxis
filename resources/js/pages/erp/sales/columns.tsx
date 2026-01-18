import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import ActionConfirmationDialog from '@/components/feedback/action-confirmation-dialog';
import DataTableColumnHeader from '@/components/table/data-table-column-header';
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
import { usePermission } from '@/hooks/use-permission';
import { formatCurrency } from '@/lib/utils';
import erp from '@/routes/erp';
import { SaleOrderStatus, saleOrderStatusLabel } from '@/types/erp/erp-enums';
import type { SaleOrderWithRelations } from '@/types/erp/sale-order';
import { Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed, DeleteIcon, Ellipsis, EyeIcon } from 'lucide-react';
import { useState } from 'react';

export const columns: ColumnDef<SaleOrderWithRelations>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'client.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Client Name" />,
        enableSorting: true,
    },
    {
        accessorKey: 'vendor.full_name', // TODO: Handle vendor name correctly when sorting
        header: ({ column }) => <DataTableColumnHeader column={column} title="Vendor" />,
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
            const variant = status === SaleOrderStatus.CANCELED ? 'destructive' : 'outline';
            const label = `Sale order status is '${saleOrderStatusLabel(status)}'`;

            return (
                <Badge
                    className="h-5 min-w-5 px-2 font-mono tabular-nums"
                    variant={variant}
                    aria-label={label}
                    title={label}
                >
                    {saleOrderStatusLabel(status)}
                </Badge>
            );
        },
        meta: {
            label: 'Sale Order Status',
            variant: 'multiSelect',
            options: Object.values(SaleOrderStatus).map((status) => ({
                label: saleOrderStatusLabel(status),
                value: status,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
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

            const salesOrder = row.original;
            const clientNameToCopy = salesOrder.client.name;
            const vendorNameToCopy = salesOrder.vendor.full_name;

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
                                    <DropdownMenuCopyButton content={clientNameToCopy}>
                                        Copy Client
                                    </DropdownMenuCopyButton>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <DropdownMenuCopyButton content={vendorNameToCopy}>
                                        Copy Vendor
                                    </DropdownMenuCopyButton>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem disabled={!can('sale.edit')} asChild>
                                    <Link
                                        className="block w-full"
                                        href={erp.salesOrders.edit(salesOrder.id)}
                                        as="button"
                                    >
                                        <EyeIcon aria-hidden size={14} /> Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    variant="destructive"
                                    disabled={!can('sale.destroy')}
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
                        description={`Are you sure you want to delete the sales order "${salesOrder.id}"? This action cannot be undone.`}
                        method="delete"
                        route={erp.salesOrders.destroy(salesOrder.id)}
                    />
                </>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
