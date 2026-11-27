import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/units';

export type UnitRow = {
    id: number;
    name: string;
    deleted_at: string | null;
    code: string;
    quantity_decimals: number;
};

const columnHelper = createAppColumnHelper<UnitRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('name', {
        header: 'Name',
        meta: { label: 'Name', variant: 'text' },
        cell: ({ getValue }) => humanize(getValue().replaceAll('-', ' ')),
    }),
    columnHelper.accessor('code', {
        header: 'Code',
        meta: { label: 'Code', variant: 'text' },
    }),
    columnHelper.accessor('quantity_decimals', {
        header: 'Quantity decimals',
        meta: { label: 'Quantity decimals', variant: 'number' },
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row }) => (
            <cell.ActionsCell actions={settingsActions(row.original, routes, 'unit-of-measure')} modal />
        ),
    }),
]);
