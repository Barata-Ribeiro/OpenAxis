import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { paymentMethodFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/payment-methods';
import type { EditFormProps } from '@/types/erp/forms';

export default function EditPaymentMethod(props: Readonly<EditFormProps & { id: number }>) {
    return <FormEditor props={props} target={routes.update.form.patch(props.id)} fields={paymentMethodFields} />;
}

EditPaymentMethod.layout = recordLayout({ title: 'Payment methods', href: routes.index() }, 'payment-method.index');
