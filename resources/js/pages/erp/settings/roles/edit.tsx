import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { roleFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/roles';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditRole(props: Readonly<EditFormProps & { id: number }>) {
    return <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={roleFields} />;
}

EditRole.layout = recordLayout({ title: 'Roles', href: routes.index() }, 'role.index');
