import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/units';

export default function ShowUnit(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowUnit.layout = recordLayout({ title: 'Units of measure', href: routes.index() }, 'unit-of-measure.index');
