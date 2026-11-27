import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/expense-categories';

import { columns } from '@/components/erp/settings/expense-categories/columns';
import type { ExpenseCategoryRow } from '@/components/erp/settings/expense-categories/columns';

export default function ExpenseCategoryIndex(props: Readonly<SettingsTableProps<ExpenseCategoryRow>>) {
    return <SettingsTable section="expense-categories" columns={columns} {...props} />;
}

ExpenseCategoryIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Expense categories', href: routes.index() },
    ],
};
