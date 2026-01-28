import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import { Button } from '@/components/ui/button';
import { Field, FieldDescription, FieldGroup, FieldLabel } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupInput, InputGroupText } from '@/components/ui/input-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { ReceivableStatus, receivableStatusLabel } from '@/types/erp/erp-enums';
import type { Receivable } from '@/types/erp/receivable';
import { Form, Link } from '@inertiajs/react';
import { DollarSign } from 'lucide-react';
import { Activity, useState } from 'react';

interface EditReceivableFormProps {
    receivable: Receivable;
}

export default function EditReceivableForm({ receivable }: Readonly<EditReceivableFormProps>) {
    const [clientId, setClientId] = useState<number | null>(receivable.client_id);
    const [dueDate, setDueDate] = useState<Date | null>(receivable.due_date ? new Date(receivable.due_date) : null);

    const paymentMethods: Receivable['payment_method'][] = ['bank_transfer', 'credit_card', 'cash', 'check'];

    return (
        <Form
            {...erp.receivables.update.form(receivable.id)}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                client_id: clientId,
                due_date: dueDate ? dueDate.toISOString().split('T')[0] : '',
            })}
        >
            {({ processing, errors }) => (
                <>
                    <Field aria-invalid={!!errors.description}>
                        <FieldLabel htmlFor="description">Description</FieldLabel>
                        <FieldDescription>A brief description of the receivable (max 255 characters).</FieldDescription>
                        <Textarea
                            id="description"
                            name="description"
                            rows={4}
                            maxLength={255}
                            defaultValue={receivable.description}
                            aria-invalid={!!errors.description}
                        />
                        <InputError message={errors.description} />
                    </Field>

                    <Field className="grid" aria-invalid={!!errors.client_id}>
                        <FieldLabel htmlFor="client_id">Client</FieldLabel>
                        <PartnerSelectCombobox
                            value={clientId}
                            setValue={setClientId}
                            route={erp.receivables.create()}
                            type="client"
                        />
                        <InputError message={errors.client_id} />
                    </Field>

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
                                    defaultValue={receivable.amount}
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
                            <Select name="payment_method" defaultValue={receivable.payment_method}>
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
                            <FieldLabel htmlFor="status">Receivable Status</FieldLabel>
                            <Select name="status" defaultValue={receivable.status}>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.status}>
                                    <SelectValue placeholder="Select a status" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(ReceivableStatus).map((status) => (
                                        <SelectItem key={status} value={status}>
                                            {receivableStatusLabel(status)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldGroup>

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <FieldDescription>
                            Additional information or comments about the receivable (max 255 characters).
                        </FieldDescription>
                        <Textarea
                            id="notes"
                            name="notes"
                            rows={4}
                            maxLength={255}
                            defaultValue={receivable.notes ?? ''}
                        />
                        <InputError message={errors.notes} />
                    </Field>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.receivables.index()} prefetch="hover" as="button">
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
