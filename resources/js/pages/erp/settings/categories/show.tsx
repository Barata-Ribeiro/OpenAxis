import { RecordView } from '@/components/erp/records/record-view';
import type { RecordViewProps } from '@/components/erp/records/record-view';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/categories';

export default function ShowCategory(props: Readonly<RecordViewProps>) {
    return <RecordView {...props} />;
}

ShowCategory.layout = recordLayout({ title: 'Product categories', href: routes.index() }, 'category.index');
