import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { formatCurrency, normalizeString } from '@/lib/utils';
import { ProductWithRelations } from '@/types/erp/product';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns/format';
import { CalendarIcon, CircleDashed } from 'lucide-react';

export const getColumns = (categories: Array<string>): Array<ColumnDef<ProductWithRelations>> => [
    {
        accessorKey: 'sku',
        header: ({ column }) => <DataTableColumnHeader column={column} title="SKU" />,
        enableSorting: true,
        enableHiding: false,
    },
    {
        id: 'cover_image',
        accessorKey: 'cover_image',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Cover Image" />,
        cell: function Cell({ row }) {
            const coverImageUrl = row.original.cover_image;
            const altText = `Miniature representation of ${row.original.name}`;

            return coverImageUrl ? (
                <img
                    src={coverImageUrl}
                    alt={altText}
                    className="aspect-square size-10 rounded-md object-cover"
                    width={40}
                    height={40}
                />
            ) : (
                <div className="flex size-10 items-center justify-center rounded-md bg-muted text-sm font-medium text-muted-foreground select-none">
                    N/A
                </div>
            );
        },
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },
    {
        id: 'category_name',
        accessorKey: 'category.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Category" />,
        meta: {
            label: 'Categories',
            variant: 'multiSelect',
            options: categories.map((c) => ({
                label: normalizeString(c),
                value: c,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
    },
    {
        accessorKey: 'cost_price',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Cost Price" />,
        cell: ({ row }) => formatCurrency(row.original.cost_price),
        enableSorting: true,
    },
    {
        accessorKey: 'selling_price',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Selling Price" />,
        cell: ({ row }) => formatCurrency(row.original.selling_price),
        enableSorting: true,
    },
    {
        accessorKey: 'current_stock',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Current Stock" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.current_stock} product(s)`}
                title={`${row.original.current_stock} product(s)`}
            >
                {row.original.current_stock}
            </Badge>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'comission',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Comission" />,
        cell: ({ row }) => `${row.original.comission}%`,
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
];
