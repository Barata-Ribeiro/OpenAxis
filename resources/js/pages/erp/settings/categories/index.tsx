import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/categories';

import { columns } from '@/components/erp/settings/categories/columns';
import type { CategoryRow } from '@/components/erp/settings/categories/columns';

export default function CategoryIndex(props: Readonly<SettingsTableProps<CategoryRow>>) {
    return <SettingsTable section="categories" columns={columns} {...props} />;
}

CategoryIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Product categories', href: routes.index() },
    ],
};
