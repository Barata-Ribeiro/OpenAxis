import { FormEditor } from '@/components/erp/forms/form-editor';
import { payableFields } from '@/lib/erp/cash-flow/fields';
import { recordLayout } from '@/lib/erp/navigation';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreatePayable(props: Readonly<EditFormProps>) {
    return <FormEditor props={props} target={payables.store.form()} fields={payableFields} />;
}

CreatePayable.layout = recordLayout(
    { title: 'Cash flow', href: cash.index() },
    'cash-entry.index',
    'financial-account.index',
);
