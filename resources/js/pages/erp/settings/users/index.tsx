import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/users';

import { columns } from '@/components/erp/settings/users/columns';
import type { UserRow } from '@/components/erp/settings/users/columns';

export default function UserIndex(props: Readonly<SettingsTableProps<UserRow>>) {
    return <SettingsTable section="users" columns={columns} {...props} />;
}

UserIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Users', href: routes.index() },
    ],
};
