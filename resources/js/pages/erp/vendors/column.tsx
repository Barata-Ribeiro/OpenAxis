import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import ActionConfirmationDialog from '@/components/feedback/action-confirmation-dialog';
import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
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
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { VendorWithRelations } from '@/types/erp/vendor';
import { Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed, DeleteIcon, EditIcon, Ellipsis, EyeIcon, XIcon } from 'lucide-react';
import { useState } from 'react';

export const columns: Array<ColumnDef<VendorWithRelations>> = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'first_name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="First Name" />,
        cell: ({ row }) => row.original.first_name,
        enableSorting: true,
    },

    {
        accessorKey: 'last_name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Last Name" />,
        cell: ({ row }) => row.original.last_name,
        enableSorting: true,
    },
    {
        accessorKey: 'user.email',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Email" />,
        cell: ({ row }) => (
            <a
                href={`mailto:${row.original.user.email}`}
                aria-label={`Send email to ${row.original.user.email}`}
                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
                {row.original.user.email}
            </a>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'commission_rate',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Commission Rate" />,
        cell: ({ row }) => `${row.original.commission_rate}%`,
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
        accessorKey: 'deleted_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Deleted At" />,
        cell: ({ row }) =>
            row.original.deleted_at ? (
                format(row.original.deleted_at, 'PPpp')
            ) : (
                <XIcon size={14} aria-label="Not deleted" className="text-muted-foreground" />
            ),
        enableSorting: true,
    },
    {
        id: 'actions',
        cell: function Cell({ row }) {
            const [open, setOpen] = useState(false);
            const { can } = usePermission();

            const nameToCopy = row.original.full_name;
            const emailToCopy = row.original.user.email;

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
                            <DropdownMenuLabel>Copy Fields</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem asChild>
                                    <DropdownMenuCopyButton content={nameToCopy}>Copy Full Name</DropdownMenuCopyButton>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <DropdownMenuCopyButton content={emailToCopy}>Copy Email</DropdownMenuCopyButton>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem disabled={!can('vendor.show')} asChild>
                                    <Link className="block w-full" href={erp.vendors.show(row.original.id)} as="button">
                                        <EyeIcon aria-hidden size={14} /> View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem disabled={!can('vendor.edit')} asChild>
                                    <Link className="block w-full" href={erp.vendors.edit(row.original.id)} as="button">
                                        <EditIcon aria-hidden size={14} /> Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    variant="destructive"
                                    disabled={!can('vendor.destroy')}
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
                        description={`Are you sure you want to soft delete vendor "${row.original.full_name}"? This action can be undone later.`}
                        method="delete"
                        route={erp.vendors.destroy(row.original.id)}
                    />
                </>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
