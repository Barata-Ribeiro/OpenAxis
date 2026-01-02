import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import VendorSelectCombobox from '@/components/helpers/vendor/vendor-select-combobox';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field';
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

export default function NewPayableForm() {
    const [supplierId, setSupplierId] = useState<number | null>(null);
    const [vendorId, setVendorId] = useState<number | null>(null);
    const [dueDate, setDueDate] = useState<Date | null>(null);

    const paymentMethods: Payable['payment_method'][] = ['bank_transfer', 'credit_card', 'cash', 'check'];

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
                due_date: dueDate?.toISOString().split('T')[0] ?? null,
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

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field aria-invalid={!!errors.supplier_id}>
                            <FieldLabel htmlFor="supplier_id">Supplier</FieldLabel>
                            <PartnerSelectCombobox
                                value={supplierId}
                                setValue={setSupplierId}
                                route={erp.payables.create()}
                            />
                            <InputError message={errors.supplier_id} />
                        </Field>

                        <Field aria-invalid={!!errors.vendor_id}>
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
                                    required
                                    aria-required
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
                            <Select name="payment_method" required aria-required>
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
                            <Select name="status" required aria-required>
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
