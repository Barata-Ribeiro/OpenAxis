import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/units';

export default function ConfirmDestroyUnit({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Archive this record?"
            confirmLabel="Archive"
        />
    );
}

ConfirmDestroyUnit.layout = recordLayout({ title: 'Units of measure', href: routes.index() }, 'unit-of-measure.index');
