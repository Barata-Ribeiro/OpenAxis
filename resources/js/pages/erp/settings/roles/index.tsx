import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/roles';

import { columns } from '@/components/erp/settings/roles/columns';
import type { RoleRow } from '@/components/erp/settings/roles/columns';

export default function RoleIndex(props: Readonly<SettingsTableProps<RoleRow>>) {
    return <SettingsTable section="roles" columns={columns} {...props} />;
}

RoleIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Roles', href: routes.index() },
    ],
};
