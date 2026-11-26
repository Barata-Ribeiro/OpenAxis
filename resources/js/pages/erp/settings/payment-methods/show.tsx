import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/payment-methods';

export default function ShowPaymentMethod(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowPaymentMethod.layout = recordLayout({ title: 'Payment methods', href: routes.index() }, 'payment-method.index');
