import InputError from '@/components/feedback/input-error';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Field, FieldLabel } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import erp from '@/routes/erp';
import { Form, Link } from '@inertiajs/react';
import { Activity, useState } from 'react';

export default function NewPayableForm() {
    const [supplierId, setSupplierId] = useState<number | null>(null);
    const [vendorId, setVendorId] = useState<number | null>(null);

    return (
        <Form
            {...erp.payables.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                supplier_id: supplierId,
                vendor_id: vendorId,
            })}
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field aria-invalid={!!errors.description}>
                        <FieldLabel htmlFor="description">Description</FieldLabel>
                        <Textarea
                            id="description"
                            name="description"
                            rows={4}
                            maxLength={255}
                            aria-invalid={!!errors.description}
                            required
                            aria-required
                        />
                        <InputError message={errors.description} />
                    </Field>

                    <Field aria-invalid={!!errors.supplier_id}>
                        <FieldLabel htmlFor="supplier_id">Supplier</FieldLabel>
                        <PartnerSelectCombobox
                            value={supplierId}
                            setValue={setSupplierId}
                            route={erp.payables.create()}
                        />
                        <InputError message={errors.supplier_id} />
                    </Field>

                    {/* TODO: Vendor selection to be implemented in the future */}
                    {/* TODO: Date picker to be implemented in the future */}
                    {/* TODO: Amount field to be implemented in the future */}
                    {/* TODO: Additional types and payment method to be implemented in the future */}

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <Textarea id="notes" name="notes" rows={4} maxLength={255} />
                        <InputError message={errors.notes} />
                    </Field>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.payables.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Create Payable
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => resetAndClearErrors()}
                                disabled={processing}
                            >
                                Reset
                            </Button>
                        </ButtonGroup>
                    </ButtonGroup>
                </>
            )}
        </Form>
    );
}
