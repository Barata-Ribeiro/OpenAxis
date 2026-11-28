import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/locations';

export type LocationRow = {
    id: number;
    name: string;
    deleted_at: string | null;
    code: string;
    is_active: 0 | 1;
};

const columnHelper = createAppColumnHelper<LocationRow>();

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
            <cell.ActionsCell actions={settingsActions(row.original, routes, 'storage-location')} modal />
        ),
    }),
]);
