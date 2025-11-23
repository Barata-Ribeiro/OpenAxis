import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { normalizeString } from '@/lib/utils';
import { ProductCategory } from '@/types/erp/product-category';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CircleDashed, Ellipsis } from 'lucide-react';

export const columns: Array<ColumnDef<ProductCategory>> = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },

    {
        accessorKey: 'description',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Description" />,
        cell: function Cell({ row }) {
            return (
                <Tooltip>
                    <TooltipTrigger asChild>
                        <p className="max-w-[20ch] truncate">{row.original.description}</p>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>{row.original.description}</p>
                    </TooltipContent>
                </Tooltip>
            );
        },
        enableSorting: false,
    },
    {
        accessorKey: 'products_count',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Products" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.products_count} product(s)`}
                title={`${row.original.products_count} product(s)`}
            >
                {row.original.products_count}
            </Badge>
        ),
        enableSorting: false,
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
            options: Object.values(['active', 'inactive']).map((status) => ({
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
            const nameToCopy = row.original.name;
            const descriptionToCopy = row.original.description;

            return (
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
                        <DropdownMenuItem asChild>
                            <DropdownMenuCopyButton content={nameToCopy}>Copy Name</DropdownMenuCopyButton>
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            <DropdownMenuCopyButton content={descriptionToCopy}>
                                Copy Description
                            </DropdownMenuCopyButton>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
