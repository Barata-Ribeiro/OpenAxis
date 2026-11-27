import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/locations';

export default function ConfirmDestroyLocation({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Archive this record?"
            confirmLabel="Archive"
        />
    );
}

ConfirmDestroyLocation.layout = recordLayout(
    { title: 'Storage locations', href: routes.index() },
    'storage-location.index',
);
