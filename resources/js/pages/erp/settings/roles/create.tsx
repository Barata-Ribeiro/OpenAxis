import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { roleFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/roles';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreateRole(props: Readonly<EditFormProps>) {
    return <FormEditor props={props} target={routes.store.form()} fields={roleFields} />;
}

CreateRole.layout = recordLayout({ title: 'Roles', href: routes.index() }, 'role.index');
