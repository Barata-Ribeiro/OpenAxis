import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { categoryFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/categories';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreateCategory(props: Readonly<EditFormProps>) {
    return <FormEditor props={props} target={routes.store.form()} fields={categoryFields} />;
}

CreateCategory.layout = recordLayout({ title: 'Product categories', href: routes.index() }, 'category.index');
