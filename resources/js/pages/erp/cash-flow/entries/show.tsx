import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import cash from '@/routes/erp/cash-flow';

export default function ShowCashEntry(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowCashEntry.layout = recordLayout(
    { title: 'Cash flow', href: cash.index() },
    'cash-entry.index',
    'financial-account.index',
);
