import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import PaymentConditionSelector from '@/components/helpers/payment-condition-selector';
import VendorSelectCombobox from '@/components/helpers/vendor/vendor-select-combobox';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupInput, InputGroupText } from '@/components/ui/input-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { SaleOrderStatus, saleOrderStatusLabel } from '@/types/erp/erp-enums';
import type { SaleOrder, SaleOrderWithRelations } from '@/types/erp/sale-order';
import { Form, Link } from '@inertiajs/react';
import { DollarSign, InfoIcon, PlusCircleIcon } from 'lucide-react';
import { Activity, useState } from 'react';

interface EditSalesOrderFormProps {
    saleOrder: Omit<SaleOrderWithRelations, 'client' | 'vendor' | 'payment_condition'>;
}

export default function EditSalesOrderForm({ saleOrder }: Readonly<EditSalesOrderFormProps>) {
    const [clientId, setClientId] = useState<number | null>(saleOrder.client_id);
    const [vendorId, setVendorId] = useState<number | null>(saleOrder.vendor_id);
    const [deliveryDate, setDeliveryDate] = useState<Date | null>(new Date(saleOrder.delivery_date));

    const paymentMethods: SaleOrder['payment_method'][] = ['bank_transfer', 'credit_card', 'debit_card', 'cash'];

    return (
        <Form
            {...erp.salesOrders.update.form(saleOrder.id)}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                client_id: clientId,
                vendor_id: vendorId,
                delivery_date: deliveryDate ? deliveryDate.toISOString().split('T')[0] : null,
            })}
        >
            {({ processing, errors }) => (
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
                            <InputError message={errors.vendor_id} />
                        </Field>

                        <PaymentConditionSelector
                            defaultValue={saleOrder.payment_condition_id}
                            errors={errors.payment_condition_id}
                        />
                    </FieldGroup>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field data-invalid={!!errors.status}>
                            <FieldLabel htmlFor="status">Sales Order Status</FieldLabel>
                            <Select name="status" required aria-required defaultValue={saleOrder.status}>
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
                            <InputError message={errors.status} />
                        </Field>

                        <Field data-invalid={!!errors.payment_method}>
                            <FieldLabel htmlFor="payment_method">Payment Method</FieldLabel>
                            <Select
                                name="payment_method"
                                required
                                aria-required
                                defaultValue={saleOrder.payment_method}
                            >
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
                            <InputError message={errors.payment_method} />
                        </Field>
                    </FieldGroup>

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <FieldDescription>
                            Any additional information regarding this sale order. (optional)
                        </FieldDescription>
                        <Textarea defaultValue={saleOrder.notes ?? ''} id="notes" name="notes" rows={4} />
                        <InputError message={errors.notes} />
                    </Field>

                    <FieldSet className="border p-4">
                        <FieldLegend>Cost Related Information</FieldLegend>
                        <FieldDescription>Adjust discount and delivery cost for this sale order.</FieldDescription>

                        <FieldGroup className="grid gap-4 sm:grid-cols-2">
                            <Field data-invalid={!!errors.discount_cost}>
                                <FieldLabel htmlFor="discount_cost">Discount Cost</FieldLabel>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <InputGroupText>
                                            <DollarSign aria-hidden />
                                        </InputGroupText>
                                    </InputGroupAddon>
                                    <InputGroupInput
                                        type="number"
                                        id="discount_cost"
                                        name="discount_cost"
                                        step="0.01"
                                        min="0"
                                        defaultValue={saleOrder.discount_cost ?? ''}
                                        aria-invalid={!!errors.discount_cost}
                                    />
                                </InputGroup>

                                <InputError message={errors.discount_cost} />
                            </Field>

                            <Field data-invalid={!!errors.delivery_cost}>
                                <FieldLabel htmlFor="delivery_cost">Delivery Cost</FieldLabel>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <InputGroupText>
                                            <DollarSign aria-hidden />
                                        </InputGroupText>
                                    </InputGroupAddon>
                                    <InputGroupInput
                                        type="number"
                                        id="delivery_cost"
                                        name="delivery_cost"
                                        step="0.01"
                                        min="0"
                                        defaultValue={saleOrder.delivery_cost ?? ''}
                                        aria-invalid={!!errors.delivery_cost}
                                    />
                                </InputGroup>

                                <InputError message={errors.delivery_cost} />
                            </Field>
                        </FieldGroup>

                        <Alert
                            className="border-blue-300 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-400"
                            aria-live="polite"
                            aria-labelledby="alert-title"
                            aria-describedby="alert-description"
                            role="alert"
                        >
                            <InfoIcon aria-hidden />
                            <AlertTitle id="alert-title">Total Amount to be recalculated!</AlertTitle>
                            <AlertDescription id="alert-description">
                                The total amount of the sale order will be recalculated based on the updated discount
                                and delivery costs.
                            </AlertDescription>
                        </Alert>
                    </FieldSet>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.salesOrders.index()} prefetch="hover" as="button">
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
