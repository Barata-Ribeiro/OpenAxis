import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/users';

export default function ConfirmDestroyUser({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Permanently delete this record?"
            confirmLabel="Delete"
        />
    );
}

ConfirmDestroyUser.layout = recordLayout({ title: 'Users', href: routes.index() }, 'user.index');
