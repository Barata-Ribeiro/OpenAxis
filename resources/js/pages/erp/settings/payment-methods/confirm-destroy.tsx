import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/payment-methods';

export default function ConfirmDestroyPaymentMethod({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Archive this record?"
            confirmLabel="Archive"
        />
    );
}

ConfirmDestroyPaymentMethod.layout = recordLayout(
    { title: 'Payment methods', href: routes.index() },
    'payment-method.index',
);
