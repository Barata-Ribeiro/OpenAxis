import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import routes from '@/routes/erp/settings/categories';

export default function ConfirmDestroyCategory({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={routes.destroy.form(id)}
            prompt="Archive this record?"
            confirmLabel="Archive"
        />
    );
}

ConfirmDestroyCategory.layout = recordLayout({ title: 'Product categories', href: routes.index() }, 'category.index');
