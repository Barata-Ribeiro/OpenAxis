import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { userFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/users';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreateUser(props: Readonly<EditFormProps>) {
    return <FormEditor props={props} target={routes.store.form()} fields={userFields(false)} />;
}

CreateUser.layout = recordLayout({ title: 'Users', href: routes.index() }, 'user.index');
