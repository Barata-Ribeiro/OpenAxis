import { FormEditor } from '@/components/erp/forms/form-editor';
import { expenseFields, receiptFields } from '@/lib/erp/cash-flow/fields';
import { recordLayout } from '@/lib/erp/navigation';
import cash from '@/routes/erp/cash-flow';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreateCashEntry({ kind, ...props }: Readonly<EditFormProps & { kind: 'receipt' | 'expense' }>) {
    const expense = kind === 'expense';

    return (
        <FormEditor
            props={props}
            target={expense ? cash.storeExpense.form() : cash.storeReceipt.form()}
            fields={expense ? expenseFields : receiptFields}
        />
    );
}

CreateCashEntry.layout = recordLayout(
    { title: 'Cash flow', href: cash.index() },
    'cash-entry.index',
    'financial-account.index',
);
