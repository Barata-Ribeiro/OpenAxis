import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { nameFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/units';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditUnit(props: Readonly<EditFormProps & { id: number }>) {
    return <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={nameFields} />;
}

EditUnit.layout = recordLayout({ title: 'Units of measure', href: routes.index() }, 'unit-of-measure.index');
