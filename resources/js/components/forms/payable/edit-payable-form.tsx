import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import VendorSelectCombobox from '@/components/helpers/vendor/vendor-select-combobox';
import { Button } from '@/components/ui/button';
import { Field, FieldDescription, FieldGroup, FieldLabel } from '@/components/ui/field';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupButton,
    InputGroupInput,
    InputGroupText,
} from '@/components/ui/input-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { PayableStatus, payableStatusLabel } from '@/types/erp/erp-enums';
import type { Payable } from '@/types/erp/payable';
import { Form, Link } from '@inertiajs/react';
import { DollarSign, HashIcon, InfoIcon } from 'lucide-react';
import { Activity, useState } from 'react';

interface EditPayableFormProps {
    payable: Payable;
}

export default function EditPayableForm({ payable }: Readonly<EditPayableFormProps>) {
    const [supplierId, setSupplierId] = useState<number | null>(payable.supplier_id);
    const [vendorId, setVendorId] = useState<number | null>(payable.vendor_id);
    const [dueDate, setDueDate] = useState<Date | null>(payable.due_date ? new Date(payable.due_date) : null);

    const paymentMethods: Payable['payment_method'][] = ['bank_transfer', 'credit_card', 'cash', 'check'];

    return (
        <Form
            {...erp.payables.update.form(payable.id)}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                supplier_id: supplierId,
                vendor_id: vendorId,
                due_date: dueDate?.toISOString().split('T')[0] ?? null,
            })}
        >
            {({ processing, errors }) => (
                <>
                    <Field aria-invalid={!!errors.description}>
                        <FieldLabel htmlFor="description">Description</FieldLabel>
                        <FieldDescription>A brief description of the payable (max 255 characters).</FieldDescription>
                        <Textarea
                            id="description"
                            name="description"
                            rows={4}
                            maxLength={255}
                            defaultValue={payable.description}
                            aria-invalid={!!errors.description}
                        />
                        <InputError message={errors.description} />
                    </Field>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field className="grid" aria-invalid={!!errors.supplier_id}>
                            <FieldLabel htmlFor="supplier_id">Supplier</FieldLabel>
                            <PartnerSelectCombobox
                                value={supplierId}
                                setValue={setSupplierId}
                                route={erp.payables.create()}
                                type="supplier"
                            />
                            <InputError message={errors.supplier_id} />
                        </Field>

                        <Field className="grid" aria-invalid={!!errors.vendor_id}>
                            <FieldLabel htmlFor="vendor_id">Vendor</FieldLabel>
                            <VendorSelectCombobox
                                value={vendorId}
                                setValue={setVendorId}
                                route={erp.payables.create()}
                            />
                            <InputError message={errors.vendor_id} />
                        </Field>
                    </FieldGroup>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field aria-invalid={!!errors.amount}>
                            <FieldLabel htmlFor="amount">Amount</FieldLabel>
                            <InputGroup>
                                <InputGroupAddon>
                                    <InputGroupText>
                                        <DollarSign aria-hidden />
                                    </InputGroupText>
                                </InputGroupAddon>
                                <InputGroupInput
                                    id="amount"
                                    name="amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    aria-invalid={!!errors.amount}
                                    defaultValue={payable.amount}
                                />
                            </InputGroup>

                            <InputError message={errors.amount} />
                        </Field>

                        <Field aria-invalid={!!errors.due_date}>
                            <FieldLabel htmlFor="due_date">Due Date</FieldLabel>
                            <CalendarDatePicker value={dueDate} setValue={setDueDate} />
                            <InputError message={errors.due_date} />
                        </Field>
                    </FieldGroup>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field data-invalid={!!errors.payment_method}>
                            <FieldLabel htmlFor="payment_method">Payment Method</FieldLabel>
                            <Select name="payment_method" defaultValue={payable.payment_method} required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.payment_method}>
                                    <SelectValue placeholder="Select a payment method" />
                                </SelectTrigger>
                                <SelectContent>
                                    {paymentMethods.map((method) => (
                                        <SelectItem key={method} value={method}>
                                            {normalizeString(method)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>
                        <Field data-invalid={!!errors.status}>
                            <FieldLabel htmlFor="status">Payable Status</FieldLabel>
                            <Select name="status" defaultValue={payable.status} required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.status}>
                                    <SelectValue placeholder="Select a status" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(PayableStatus).map((status) => (
                                        <SelectItem key={status} value={status}>
                                            {payableStatusLabel(status)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldGroup>

                    <Field aria-invalid={!!errors.reference_number}>
                        <FieldLabel htmlFor="reference_number">Reference Number</FieldLabel>
                        <InputGroup>
                            <InputGroupAddon>
                                <InputGroupText>
                                    <HashIcon aria-hidden />
                                </InputGroupText>
                            </InputGroupAddon>
                            <InputGroupInput
                                type="text"
                                id="reference_number"
                                name="reference_number"
                                maxLength={50}
                                aria-invalid={!!errors.reference_number}
                                defaultValue={payable.reference_number ?? ''}
                            />
                            <InputGroupAddon align="inline-end">
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <InputGroupButton className="rounded-full" size="icon-xs">
                                            <InfoIcon aria-hidden />
                                        </InputGroupButton>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        A unique reference number to help identify the payable.
                                    </TooltipContent>
                                </Tooltip>
                            </InputGroupAddon>
                        </InputGroup>
                        <InputError message={errors.reference_number} />
                    </Field>

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <FieldDescription>
                            Additional information or comments about the payable (max 255 characters).
                        </FieldDescription>
                        <Textarea id="notes" name="notes" rows={4} maxLength={255} defaultValue={payable.notes ?? ''} />
                        <InputError message={errors.notes} />
                    </Field>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.payables.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>

                        <Button type="submit" disabled={processing}>
                            <Activity mode={processing ? 'visible' : 'hidden'}>
                                <Spinner />
                            </Activity>
                            Save
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
