import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/locations';

import { columns } from '@/components/erp/settings/locations/columns';
import type { LocationRow } from '@/components/erp/settings/locations/columns';

export default function LocationIndex(props: Readonly<SettingsTableProps<LocationRow>>) {
    return <SettingsTable section="locations" columns={columns} {...props} />;
}

LocationIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Storage locations', href: routes.index() },
    ],
};
