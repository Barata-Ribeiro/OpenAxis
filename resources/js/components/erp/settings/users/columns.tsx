import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/users';

export type UserRow = {
    id: number;
    name: string;
    email: string;
};

const columnHelper = createAppColumnHelper<UserRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('name', {
        header: 'Name',
        meta: { label: 'Name', variant: 'text' },
        cell: ({ getValue }) => humanize(getValue().replaceAll('-', ' ')),
    }),
    columnHelper.accessor('email', {
        header: 'Email',
        meta: { label: 'Email', variant: 'text' },
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row }) => <cell.ActionsCell actions={settingsActions(row.original, routes, 'user')} modal />,
    }),
]);
