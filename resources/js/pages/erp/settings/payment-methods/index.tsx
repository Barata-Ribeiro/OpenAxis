import { SettingsTable } from '@/components/erp/settings/settings-table';
import type { SettingsTableProps } from '@/components/erp/settings/settings-table';
import { dashboard } from '@/routes';
import routes from '@/routes/erp/settings/payment-methods';

import { columns } from '@/components/erp/settings/payment-methods/columns';
import type { PaymentMethodRow } from '@/components/erp/settings/payment-methods/columns';

export default function PaymentMethodIndex(props: Readonly<SettingsTableProps<PaymentMethodRow>>) {
    return <SettingsTable section="payment-methods" columns={columns} {...props} />;
}

PaymentMethodIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Payment methods', href: routes.index() },
    ],
};
