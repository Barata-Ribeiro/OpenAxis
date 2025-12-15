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
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { usePermission } from '@/hooks/use-permission';
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import type { PaymentCondition } from '@/types/erp/payment-condition';
import { Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CircleDashed, DeleteIcon, EditIcon, Ellipsis } from 'lucide-react';
import { useState } from 'react';

export const columns: ColumnDef<Required<PaymentCondition>>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'code',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Code" />,
        enableSorting: true,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },
    {
        accessorKey: 'days_until_due',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Days Until Due" />,
        cell: ({ row }) => `${row.original.days_until_due} day(s)`,
        enableSorting: true,
    },
    {
        accessorKey: 'installments',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Installments" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.installments} installment(s)`}
                title={`${row.original.installments} installment(s)`}
            >
                {row.original.installments}
            </Badge>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'is_active',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        cell: function Cell({ row }) {
            const isActive = row.original.is_active;
            const statusLabel = isActive ? 'Active' : 'Inactive';
            const statusVariant = isActive ? 'secondary' : 'destructive';

            return (
                <Badge variant={statusVariant} className="select-none">
                    {statusLabel}
                </Badge>
            );
        },
        meta: {
            label: 'Active Status',
            variant: 'select',
            options: ['active', 'inactive'].map((status) => ({
                label: normalizeString(status),
                value: status === 'active' ? 'true' : 'false',
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

            return (
                <>
                    <DropdownMenu modal={false}>
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
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem disabled={!can('finance.edit')} asChild>
                                    <Link
                                        className="block w-full"
                                        href={erp.paymentConditions.edit(row.original.code)}
                                        as="button"
                                    >
                                        <EditIcon aria-hidden size={14} /> Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    variant="destructive"
                                    disabled={!can('finance.destroy')}
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
                        description={`Are you sure you want to delete the payment condition "${row.original.name}"? This action cannot be undone.`}
                        method="delete"
                        route={erp.paymentConditions.destroy(row.original.code)}
                    />
                </>
            );
        },
    },
];
