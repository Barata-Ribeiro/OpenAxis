import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/roles';

export default function ConfirmDestroyRole({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Permanently delete this record?"
            confirmLabel="Delete"
        />
    );
}

ConfirmDestroyRole.layout = recordLayout({ title: 'Roles', href: routes.index() }, 'role.index');
