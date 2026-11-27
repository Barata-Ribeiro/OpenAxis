import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/locations';

export default function ShowLocation(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowLocation.layout = recordLayout({ title: 'Storage locations', href: routes.index() }, 'storage-location.index');
