import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { nameFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/expense-categories';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditExpenseCategory(props: Readonly<EditFormProps & { id: number }>) {
    return <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={nameFields} />;
}

EditExpenseCategory.layout = recordLayout(
    { title: 'Expense categories', href: routes.index() },
    'expense-category.index',
);
