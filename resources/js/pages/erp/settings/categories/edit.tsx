import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { categoryFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/categories';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditCategory(props: Readonly<EditFormProps & { id: number }>) {
    return <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={categoryFields} />;
}

EditCategory.layout = recordLayout({ title: 'Product categories', href: routes.index() }, 'category.index');
