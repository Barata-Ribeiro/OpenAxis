import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import PaymentConditionSelector from '@/components/helpers/payment-condition-selector';
import VendorSelectCombobox from '@/components/helpers/vendor/vendor-select-combobox';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { SaleOrderStatus, saleOrderStatusLabel } from '@/types/erp/erp-enums';
import type { SaleOrder } from '@/types/erp/sale-order';
import { Form, Link } from '@inertiajs/react';
import { PlusCircleIcon } from 'lucide-react';
import { Activity, useState } from 'react';

export default function NewSalesOrderForm() {
    const [clientId, setClientId] = useState<number | null>(null);
    const [vendorId, setVendorId] = useState<number | null>(null);
    const [deliveryDate, setDeliveryDate] = useState<Date | null>(null);

    const paymentMethods: SaleOrder['payment_method'][] = ['bank_transfer', 'credit_card', 'debit_card', 'cash'];

    return (
        <Form
            {...erp.salesOrders.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                client_id: clientId,
            })}
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field data-invalid={!!errors.client_id}>
                            <FieldLabel htmlFor="client_id">Client</FieldLabel>

                            <ButtonGroup className="w-full">
                                <PartnerSelectCombobox
                                    value={clientId}
                                    setValue={setClientId}
                                    route={erp.salesOrders.create()}
                                    type="client"
                                />
                                <Button
                                    variant="outline"
                                    aria-label="Register a new client"
                                    title="Register a new client"
                                    asChild
                                >
                                    <Link href={erp.clients.create()} prefetch="hover" as="button">
                                        <PlusCircleIcon aria-hidden />
                                    </Link>
                                </Button>
                            </ButtonGroup>
                            <InputError message={errors.client_id} />
                        </Field>

                        <Field data-invalid={!!errors.delivery_date}>
                            <FieldLabel htmlFor="delivery_date">Delivery Date</FieldLabel>
                            <CalendarDatePicker
                                value={deliveryDate}
                                setValue={setDeliveryDate}
                                limitToCurrentYearAndFuture={false}
                            />
                            <InputError message={errors.delivery_date} />
                        </Field>
                    </FieldGroup>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field data-invalid={!!errors.vendor_id}>
                            <FieldLabel htmlFor="vendor_id">Salesperson</FieldLabel>

                            <ButtonGroup className="w-full">
                                <VendorSelectCombobox
                                    value={vendorId}
                                    setValue={setVendorId}
                                    route={erp.salesOrders.create()}
                                />

                                <Button
                                    variant="outline"
                                    aria-label="Register a new vendor"
                                    title="Register a new vendor"
                                    asChild
                                >
                                    <Link href={erp.vendors.create()} prefetch="hover" as="button">
                                        <PlusCircleIcon aria-hidden />
                                    </Link>
                                </Button>
                            </ButtonGroup>
                        </Field>

                        <PaymentConditionSelector errors={errors.payment_condition_id} />
                    </FieldGroup>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field data-invalid={!!errors.status}>
                            <FieldLabel htmlFor="status">Sales Order Status</FieldLabel>
                            <Select name="status" required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.status}>
                                    <SelectValue placeholder="Select a status" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(SaleOrderStatus).map((status) => (
                                        <SelectItem key={status} value={status}>
                                            {saleOrderStatusLabel(status)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>

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
                    </FieldGroup>

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <Textarea id="notes" name="notes" rows={4} />
                        <InputError message={errors.notes} />
                    </Field>

                    {/* TODO: Products selection component here */}

                    <FieldSet className="rounded-md border p-4" aria-labelledby="update-accounts-legend">
                        <FieldLegend id="update-accounts-legend">Update Accounts Automatically</FieldLegend>

                        <FieldGroup className="grid gap-4 sm:grid-cols-2">
                            <Label className="flex items-start gap-3 rounded-lg border p-3 hover:bg-accent/50 has-aria-checked:border-red-600 has-aria-checked:bg-red-50 dark:has-aria-checked:border-red-900 dark:has-aria-checked:bg-red-950">
                                <Checkbox
                                    id="update_payables"
                                    name="update_payables"
                                    className="data-[state=checked]:border-red-600 data-[state=checked]:bg-red-600 data-[state=checked]:text-white dark:data-[state=checked]:border-red-700 dark:data-[state=checked]:bg-red-700"
                                />
                                <p className="text-sm leading-none font-medium">Update Payables to Paid</p>
                            </Label>

                            <Label className="flex items-start gap-3 rounded-lg border p-3 hover:bg-accent/50 has-aria-checked:border-red-600 has-aria-checked:bg-red-50 dark:has-aria-checked:border-red-900 dark:has-aria-checked:bg-red-950">
                                <Checkbox
                                    id="update_receivables"
                                    name="update_receivables"
                                    className="data-[state=checked]:border-red-600 data-[state=checked]:bg-red-600 data-[state=checked]:text-white dark:data-[state=checked]:border-red-700 dark:data-[state=checked]:bg-red-700"
                                />
                                <p className="text-sm leading-none font-medium">Update Receivables to Paid</p>
                            </Label>
                        </FieldGroup>
                    </FieldSet>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.salesOrders.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Register Sale
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    resetAndClearErrors();
                                    setClientId(null);
                                }}
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
