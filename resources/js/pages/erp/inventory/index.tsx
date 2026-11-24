import DataTable, { type ServerTableData } from '@/components/data-table/data-table';
import { Badge } from '@/components/ui/badge';
import { index as inventory } from '@/routes/erp/inventory';
import movements from '@/routes/erp/inventory/movements';
import products from '@/routes/erp/inventory/products';
import { ImageIcon, PackagePlus, Plus } from 'lucide-react';
import { dashboard } from '@/routes';
import { ErpDate, ErpMoney, ErpQuantity, useErpFormatting } from '@/components/erp/format';
import { actionColumn, choices, ErpLink, ErpPage, humanize, Metrics, tableColumn } from '@/components/erp/page';

type ProductRow = {
    id: number;
    sku: string;
    image: string | null;
    name: string;
    quantity: number;
    unit: string;
    expires_on: string | null;
    days_remaining: number | null;
    status: string;
    minimum_stock: number;
    category: string | null;
    average_cost: number;
    current_value: number;
};

const statuses = ['in_stock', 'out_of_stock', 'critical', 'expiring', 'expired', 'inactive'];
const column = tableColumn<ProductRow>;
const columns = [
    column('sku', 'SKU'),
    {
        id: 'image',
        meta: { label: 'Image' },
        cell: ({ row }: { row: { original: ProductRow } }) =>
            row.original.image ? (
                <img
                    src={row.original.image}
                    alt=""
                    width={36}
                    height={36}
                    loading="lazy"
                    className="size-9 rounded-md object-cover"
                />
            ) : (
                <span className="grid size-9 place-items-center rounded-md border bg-muted text-muted-foreground">
                    <ImageIcon aria-hidden className="size-4" />
                </span>
            ),
    },
    column('name', 'Name'),
    column('quantity', 'Current quantity', 'number', (row) => <ErpQuantity amount={row.quantity} />),
    column('unit', 'Unit of measure'),
    column('expires_on', 'Expiry date', 'date', (row) => <ErpDate value={row.expires_on} />),
    column('days_remaining', 'Days remaining', 'number'),
    column(
        'status',
        'Status',
        'select',
        (row) => (
            <Badge variant={['critical', 'expired', 'out_of_stock'].includes(row.status) ? 'destructive' : 'secondary'}>
                {humanize(row.status)}
            </Badge>
        ),
        choices(statuses),
    ),
    column('minimum_stock', 'Minimum stock', 'number', (row) => <ErpQuantity amount={row.minimum_stock} />),
    column('category', 'Category'),
    column('average_cost', 'Average unit cost', 'number', (row) => <ErpMoney amount={row.average_cost} />),
    column('current_value', 'Current value', 'number', (row) => <ErpMoney amount={row.current_value} />),
    actionColumn<ProductRow>((row) => (
        <>
            <ErpLink href={products.show.url(row.id)} permission="product.show" compact modal>
                View
            </ErpLink>
            <ErpLink href={products.edit.url(row.id)} permission="product.edit" compact modal>
                Edit
            </ErpLink>
            {row.quantity === 0 && (
                <ErpLink href={products.archive.url(row.id)} permission="product.destroy" compact modal>
                    Archive
                </ErpLink>
            )}
        </>
    )),
];

type Summary = Record<'products' | 'critical' | 'in_stock' | 'expiring' | 'expired', number>;

export default function Inventory({
    summary,
    table,
}: Readonly<{ summary: Summary; table: ServerTableData<ProductRow> }>) {
    const { quantity } = useErpFormatting();
    return (
        <ErpPage
            title="Inventory"
            actions={
                <>
                    <ErpLink href={products.create.url()} permission="product.create">
                        <Plus /> New product
                    </ErpLink>
                    <ErpLink href={movements.receipt.url()} permission="stock-movement.create" primary>
                        <PackagePlus /> New stock receipt
                    </ErpLink>
                </>
            }
        >
            <Metrics
                items={[
                    { title: 'Total products', value: quantity.format(summary.products) },
                    { title: 'Critical stock', value: quantity.format(summary.critical) },
                    { title: 'Items in stock', value: quantity.format(summary.in_stock) },
                    { title: 'Expiring within 30 days', value: quantity.format(summary.expiring) },
                    { title: 'Expired', value: quantity.format(summary.expired) },
                ]}
            />
            <DataTable columns={columns} result={table} url={inventory.url()} label="Inventory products" />
        </ErpPage>
    );
}

Inventory.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Inventory', href: inventory() },
    ],
};
