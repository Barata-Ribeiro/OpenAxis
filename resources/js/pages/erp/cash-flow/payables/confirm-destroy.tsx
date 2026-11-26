import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';

export default function ConfirmDestroyPayable({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={payables.destroy.form(id)}
            prompt="Cancel this unpaid obligation?"
            confirmLabel="Cancel payable"
        />
    );
}

ConfirmDestroyPayable.layout = recordLayout(
    { title: 'Cash flow', href: cash.index() },
    'cash-entry.index',
    'financial-account.index',
);
