import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import movements from '@/routes/erp/inventory/movements';

export default function ShowMovement(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowMovement.layout = recordLayout({ title: 'Stock movements', href: movements.index() }, 'stock-movement.index');
