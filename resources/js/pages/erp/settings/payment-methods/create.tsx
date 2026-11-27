import { FormEditor } from '@/components/erp/forms/form-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { paymentMethodFields } from '@/lib/erp/settings/fields';
import routes from '@/routes/erp/settings/payment-methods';
import type { EditFormProps } from '@/types/erp/forms';

export default function CreatePaymentMethod(props: Readonly<EditFormProps>) {
    return <FormEditor props={props} target={routes.store.form()} fields={paymentMethodFields} />;
}

CreatePaymentMethod.layout = recordLayout({ title: 'Payment methods', href: routes.index() }, 'payment-method.index');
