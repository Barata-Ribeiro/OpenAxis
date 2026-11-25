import DataTable, { type ServerTableData } from '@/components/data-table/data-table';
import movements from '@/routes/erp/inventory/movements';
import { actionColumn, choices, ErpLink, ErpPage, humanize, tableColumn } from '@/components/erp/page';
import { ErpDate, ErpQuantity } from '@/components/erp/format';
import { dashboard } from '@/routes';
import { Eye } from 'lucide-react';

type MovementRow = {
    id: number;
    occurred_at: string;
    type: string;
    product: string;
    batch: string;
    location: string;
    quantity: string;
};

const column = tableColumn<MovementRow>;
const columns = [
    column('occurred_at', 'Date and time', 'date', (row) => <ErpDate value={row.occurred_at} withTime />),
    column('type', 'Type', 'select', (row) => humanize(row.type), choices(['opening', 'receipt', 'issue', 'return'])),
    column('product', 'Product'),
    column('batch', 'Batch'),
    column('location', 'Location'),
    column('quantity', 'Quantity', 'number', (row) => <ErpQuantity amount={Number(row.quantity)} />),
    actionColumn<MovementRow>((row) => [
        { label: 'View', href: movements.show.url(row.id), permissions: ['stock-movement.show'], icon: Eye },
    ]),
];

export default function Movements({ table }: Readonly<{ table: ServerTableData<MovementRow> }>) {
    return (
        <ErpPage
            title="Stock movements"
            actions={
                <>
                    <ErpLink href={movements.issue.url()} permission="stock-movement.create">
                        New stock issue
                    </ErpLink>
                    <ErpLink href={movements.receipt.url()} permission="stock-movement.create" primary>
                        New stock receipt
                    </ErpLink>
                </>
            }
        >
            <DataTable columns={columns} result={table} url={movements.index.url()} label="Stock movements" />
        </ErpPage>
    );
}

Movements.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Stock movements', href: movements.index() },
    ],
};
