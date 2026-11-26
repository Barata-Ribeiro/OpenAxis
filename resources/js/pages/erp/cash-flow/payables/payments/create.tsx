import { FormEditor } from '@/components/erp/forms/form-editor';
import { RecordDetails } from '@/components/erp/records/record-details';
import type { RecordDetailsProps } from '@/components/erp/records/record-details';
import { paymentFields } from '@/lib/erp/cash-flow/fields';
import { recordLayout } from '@/lib/erp/navigation';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreatePayablePayment({
    id,
    details,
    ...props
}: Readonly<EditFormProps & RecordDetailsProps & { id: number; values: { expected_amount_cents: number } }>) {
    return (
        <FormEditor
            props={props}
            target={payables.pay.form(id)}
            fields={paymentFields}
            submitLabel="Confirm payment"
            hidden={{ expected_amount_cents: props.values.expected_amount_cents }}
        >
            <RecordDetails details={details} />
        </FormEditor>
    );
}

CreatePayablePayment.layout = recordLayout(
    { title: 'Cash flow', href: cash.index() },
    'cash-entry.index',
    'financial-account.index',
);
