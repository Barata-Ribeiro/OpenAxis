import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/users';

export default function ShowUser(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowUser.layout = recordLayout({ title: 'Users', href: routes.index() }, 'user.index');
