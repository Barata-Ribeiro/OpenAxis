import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
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
import { PayableStatus, payableStatusLabel } from '@/types/erp/erp-enums';
import type { PayableWithRelations } from '@/types/erp/payable';
import { Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed, Ellipsis, EyeIcon } from 'lucide-react';

export const columns: ColumnDef<PayableWithRelations>[] = [
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
        accessorKey: 'supplier.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Supplier" />,
        enableSorting: true,
    },
    {
        accessorKey: 'amount',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Amount" />,
        cell: ({ row }) => formatCurrency(row.original.amount),
        enableSorting: true,
    },
    {
        accessorKey: 'due_date',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Due Date" />,
        cell: ({ row }) => format(row.original.due_date, 'PPpp'),
        meta: {
            label: 'Due Date',
            variant: 'dateRange',
            icon: CalendarIcon,
        },
        enableSorting: true,
    },
    {
        accessorKey: 'status',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        cell: function Cell({ row }) {
            const rawStatus = row.original.status;

            return <Badge variant="secondary">{payableStatusLabel(rawStatus)}</Badge>;
        },
        meta: {
            label: 'Status',
            variant: 'multiSelect',
            options: Object.values(PayableStatus).map((status) => ({
                label: payableStatusLabel(status),
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
            const { can } = usePermission();

            const payable = row.original;

            const codeToCopy = payable.code;
            const supplierNameToCopy = payable.supplier.name;

            return (
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
                                <DropdownMenuCopyButton content={codeToCopy}>Copy Code</DropdownMenuCopyButton>
                            </DropdownMenuItem>
                            <DropdownMenuItem asChild>
                                <DropdownMenuCopyButton content={supplierNameToCopy}>
                                    Copy Supplier
                                </DropdownMenuCopyButton>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                        <DropdownMenuGroup>
                            <DropdownMenuItem disabled={!can('finance.show')} asChild>
                                <Link className="block w-full" href={erp.payables.show(payable.id)} as="button">
                                    <EyeIcon aria-hidden size={14} /> View
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem disabled={!can('finance.edit')} asChild>
                                <Link className="block w-full" href={erp.payables.edit(payable.id)} as="button">
                                    <EyeIcon aria-hidden size={14} /> Edit
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
