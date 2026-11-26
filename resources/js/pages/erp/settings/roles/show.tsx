import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/roles';

export default function ShowRole(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowRole.layout = recordLayout({ title: 'Roles', href: routes.index() }, 'role.index');
