import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/expense-categories';

export default function ConfirmDestroyExpenseCategory({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Archive this record?"
            confirmLabel="Archive"
        />
    );
}

ConfirmDestroyExpenseCategory.layout = recordLayout(
    { title: 'Expense categories', href: routes.index() },
    'expense-category.index',
);
