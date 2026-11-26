import InputError from '@/components/common/input-error';
import { InputField, SelectField, TextareaField } from '@/components/erp/forms/fields';
import { scaleFields } from '@/lib/erp/forms';
import type { Option } from '@/types/erp/forms';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { FieldGroup } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import movements from '@/routes/erp/inventory/movements';
import { Form, Head } from '@inertiajs/react';
import { Activity } from 'react';

type Props = { direction: 'receipt' | 'issue'; options: Option[]; locations: Option[]; occurredAt: string };

export default function CreateMovement({ direction, options, locations, occurredAt }: Readonly<Props>) {
    const receipt = direction === 'receipt';
    const title = receipt ? 'New stock receipt' : 'New stock issue';

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title={title} />
            <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
            <Card className="w-full max-w-3xl">
                <CardContent>
                    <Form
                        {...(receipt ? movements.storeReceipt.form() : movements.storeIssue.form())}
                        transform={(data) => scaleFields(data, { quantity_milliunits: 3, cost_cents: 2 })}
                        className="space-y-5"
                        disableWhileProcessing
                    >
                        {({ errors, processing }) => (
                            <>
                                <FieldGroup>
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        {receipt ? (
                                            <SelectField
                                                name="product_id"
                                                label="Product"
                                                options={options}
                                                required
                                                error={errors['product_id']}
                                            />
                                        ) : (
                                            <SelectField
                                                name="product_batch_id"
                                                label="Batch"
                                                options={options}
                                                required
                                                error={errors['product_batch_id']}
                                            />
                                        )}
                                        <SelectField
                                            name="storage_location_id"
                                            label="Storage location"
                                            options={locations}
                                            required
                                            error={errors['storage_location_id']}
                                        />
                                        <InputField
                                            name="quantity_milliunits"
                                            label="Quantity"
                                            type="number"
                                            min={0.001}
                                            step={0.001}
                                            required
                                            error={errors['quantity_milliunits']}
                                        />
                                        <InputField
                                            name="occurred_at"
                                            label="Date and time (UTC)"
                                            type="datetime-local"
                                            defaultValue={occurredAt}
                                            required
                                            error={errors['occurred_at']}
                                        />
                                        <Activity mode={receipt ? 'visible' : 'hidden'}>
                                            <InputField
                                                name="supplier_lot_code"
                                                label="Supplier lot code"
                                                error={errors['supplier_lot_code']}
                                            />
                                            <InputField
                                                name="expires_on"
                                                label="Expiry date"
                                                type="date"
                                                error={errors['expires_on']}
                                            />
                                            <InputField
                                                name="cost_cents"
                                                label="Total acquisition cost (BRL)"
                                                type="number"
                                                min={0}
                                                step={0.01}
                                                error={errors['cost_cents']}
                                            />
                                        </Activity>
                                    </div>
                                    <TextareaField name="notes" label="Notes" error={errors['notes']} />
                                </FieldGroup>
                                <InputError role="alert" message={errors['record']} />
                                <Button type="submit" isDisabled={processing}>
                                    <Activity mode={processing ? 'visible' : 'hidden'}>
                                        <Spinner />
                                    </Activity>
                                    {receipt ? 'Record receipt' : 'Record issue'}
                                </Button>
                            </>
                        )}
                    </Form>
                </CardContent>
            </Card>
        </section>
    );
}

CreateMovement.layout = (props: Props & { auth: { permissions: string[] } }) => ({
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        ...(props.auth.permissions.includes('stock-movement.index')
            ? [{ title: 'Stock movements', href: movements.index() }]
            : []),
        props.direction === 'receipt'
            ? { title: 'New stock receipt', href: movements.receipt() }
            : { title: 'New stock issue', href: movements.issue() },
    ],
});
