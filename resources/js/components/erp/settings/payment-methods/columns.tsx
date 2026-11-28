import { ErpFee } from '@/components/erp/format';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/payment-methods';

export type PaymentMethodRow = {
    id: number;
    name: string;
    deleted_at: string | null;
    fee_type: string;
    fee_rate: number;
    is_active: 0 | 1;
};

const columnHelper = createAppColumnHelper<PaymentMethodRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('name', {
        header: 'Name',
        meta: { label: 'Name', variant: 'text' },
        cell: ({ getValue }) => humanize(getValue().replaceAll('-', ' ')),
    }),
    columnHelper.accessor('fee_type', {
        header: 'Fee type',
        meta: { label: 'Fee type', variant: 'text' },
        cell: ({ getValue }) => humanize(getValue()),
    }),
    columnHelper.accessor('fee_rate', {
        header: 'Fee rate',
        meta: { label: 'Fee rate', variant: 'number' },
        cell: ({ row, getValue }) => <ErpFee rate={getValue()} type={row.original.fee_type} />,
    }),
    columnHelper.accessor('is_active', {
        header: 'Active',
        meta: { label: 'Active', variant: 'number' },
        cell: ({ cell }) => <cell.ActiveBadgeCell />,
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row }) => (
            <cell.ActionsCell actions={settingsActions(row.original, routes, 'payment-method')} modal />
        ),
    }),
]);
