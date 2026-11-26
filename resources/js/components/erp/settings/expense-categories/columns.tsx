import { createAppColumnHelper } from '@/hooks/data-table/table';
import { humanize } from '@/lib/erp/format';
import { settingsActions } from '@/lib/erp/settings/actions';
import routes from '@/routes/erp/settings/expense-categories';

export type ExpenseCategoryRow = {
    id: number;
    name: string;
    deleted_at: string | null;
    is_system: 0 | 1;
};

const columnHelper = createAppColumnHelper<ExpenseCategoryRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('name', {
        header: 'Name',
        meta: { label: 'Name', variant: 'text' },
        cell: ({ getValue }) => humanize(getValue().replaceAll('-', ' ')),
    }),
    columnHelper.accessor('is_system', {
        header: 'System category',
        meta: { label: 'System category', variant: 'number' },
        cell: ({ getValue }) => (getValue() ? 'Yes' : 'No'),
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row }) => (
            <cell.ActionsCell
                actions={settingsActions(row.original, routes, 'expense-category', {
                    canRemove: !row.original.is_system,
                })}
                modal
            />
        ),
    }),
]);
