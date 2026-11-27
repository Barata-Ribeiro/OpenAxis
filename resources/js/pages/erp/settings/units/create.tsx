import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { unitFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/units';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreateUnit(props: Readonly<EditFormProps>) {
    return <FormEditor props={props} target={routes.store.form()} fields={unitFields} />;
}

CreateUnit.layout = recordLayout({ title: 'Units of measure', href: routes.index() }, 'unit-of-measure.index');
