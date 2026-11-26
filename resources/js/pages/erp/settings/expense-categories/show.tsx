import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/expense-categories';

export default function ShowExpenseCategory(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowExpenseCategory.layout = recordLayout(
    { title: 'Expense categories', href: routes.index() },
    'expense-category.index',
);
