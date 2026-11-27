import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { locationFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/locations';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditLocation(props: Readonly<EditFormProps & { id: number }>) {
    return <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={locationFields} />;
}

EditLocation.layout = recordLayout({ title: 'Storage locations', href: routes.index() }, 'storage-location.index');
