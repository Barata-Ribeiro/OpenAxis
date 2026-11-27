import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { userFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/users';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditUser(props: Readonly<EditFormProps & { id: number; self?: boolean }>) {
    return (
        <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={userFields(true, props.self)} />
    );
}

EditUser.layout = recordLayout({ title: 'Users', href: routes.index() }, 'user.index');
