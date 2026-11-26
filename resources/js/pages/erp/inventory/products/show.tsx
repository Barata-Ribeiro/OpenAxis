import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import { index as inventory } from '@/routes/erp/inventory';

export default function ShowProduct(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowProduct.layout = recordLayout({ title: 'Inventory', href: inventory() }, 'product.index');
