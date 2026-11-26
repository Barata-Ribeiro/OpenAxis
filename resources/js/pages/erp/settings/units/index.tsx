import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/units';

import { columns } from '@/components/erp/settings/units/columns';
import type { UnitRow } from '@/components/erp/settings/units/columns';

export default function UnitIndex(props: Readonly<SettingsTableProps<UnitRow>>) {
    return <SettingsTable section="units" columns={columns} {...props} />;
}

UnitIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Units of measure', href: routes.index() },
    ],
};
