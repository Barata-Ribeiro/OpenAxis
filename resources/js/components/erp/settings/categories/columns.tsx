import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/categories';

export type CategoryRow = {
    id: number;
    name: string;
    deleted_at: string | null;
};

const columnHelper = createAppColumnHelper<CategoryRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('name', {
        header: 'Name',
        meta: { label: 'Name', variant: 'text' },
        cell: ({ getValue }) => humanize(getValue().replaceAll('-', ' ')),
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row }) => <cell.ActionsCell actions={settingsActions(row.original, routes, 'category')} modal />,
    }),
]);
