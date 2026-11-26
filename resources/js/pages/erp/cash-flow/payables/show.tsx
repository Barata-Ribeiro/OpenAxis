import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import cash from '@/routes/erp/cash-flow';

export default function ShowPayable(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowPayable.layout = recordLayout(
    { title: 'Cash flow', href: cash.index() },
    'cash-entry.index',
    'financial-account.index',
);
