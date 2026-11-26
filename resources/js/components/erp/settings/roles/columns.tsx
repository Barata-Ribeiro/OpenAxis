import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/roles';

export type RoleRow = {
    id: number;
    name: string;
};

const columnHelper = createAppColumnHelper<RoleRow>();

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
        cell: ({ cell, row }) => (
            <cell.ActionsCell
                actions={settingsActions(row.original, routes, 'role', {
                    canEdit: row.original.name !== 'super-admin',
                    canRemove: row.original.name !== 'super-admin',
                })}
                modal
            />
        ),
    }),
]);
