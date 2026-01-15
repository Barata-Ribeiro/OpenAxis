import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import PaymentConditionSelector from '@/components/helpers/payment-condition-selector';
import ItemsForSalesOrder, { type SelectedProduct } from '@/components/helpers/sales-order/items-for-sales-order';
import VendorSelectCombobox from '@/components/helpers/vendor/vendor-select-combobox';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { formatCurrency, normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { SaleOrderStatus, saleOrderStatusLabel } from '@/types/erp/erp-enums';
import type { SaleOrder } from '@/types/erp/sale-order';
import { Form, Link } from '@inertiajs/react';
import { AlertCircleIcon, PlusCircleIcon } from 'lucide-react';
import { Activity, useState } from 'react';

export default function NewSalesOrderForm() {
    const [clientId, setClientId] = useState<number | null>(null);
    const [vendorId, setVendorId] = useState<number | null>(null);
    const [selectedProducts, setSelectedProducts] = useState<SelectedProduct[]>([]);
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
                vendor_id: vendorId,
                delivery_date: deliveryDate ? deliveryDate.toISOString().split('T')[0] : null,
                items: selectedProducts.map((product) => {
                    const unitPrice = Number(product.selling_price);
                    const quantity = Number(product.quantity);
                    const subtotalPrice = unitPrice * quantity;

                    const commissionRate = Number(product.comission);
                    const commissionItem =
                        subtotalPrice * ((Number.isFinite(commissionRate) ? commissionRate : 0) / 100);

                    return {
                        product_id: product.id,
                        quantity,
                        unit_price: unitPrice,
                        subtotal_price: subtotalPrice,
                        commission_item: commissionItem,
                    };
                }),
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
                            <InputError message={errors.vendor_id} />
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
                            <InputError message={errors.status} />
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
                            <InputError message={errors.payment_method} />
                        </Field>
                    </FieldGroup>

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <FieldDescription>
                            Any additional information regarding this sale order. (optional)
                        </FieldDescription>
                        <Textarea id="notes" name="notes" rows={4} />
                        <InputError message={errors.notes} />
                    </Field>

                    <ItemsForSalesOrder
                        value={selectedProducts}
                        setValue={setSelectedProducts}
                        route={erp.salesOrders.create()}
                        errors={errors.items}
                    />

                    <div className="grid gap-2 text-right text-3xl">
                        <p>
                            Total:{' '}
                            <span className="font-mono font-semibold">
                                {formatCurrency(
                                    selectedProducts.reduce(
                                        (total, product) =>
                                            total + Number(product.selling_price) * Number(product.quantity),
                                        0,
                                    ),
                                )}
                            </span>
                        </p>
                        <p>
                            Total Comission:{' '}
                            <span className="font-mono font-semibold">
                                {formatCurrency(
                                    selectedProducts.reduce((total, product) => {
                                        const unitPrice = Number(product.selling_price);
                                        const quantity = Number(product.quantity);
                                        const subtotal = unitPrice * quantity;

                                        const commissionRate = Number(product.comission);
                                        const commissionAmount =
                                            subtotal * ((Number.isFinite(commissionRate) ? commissionRate : 0) / 100);
                                        return total + commissionAmount;
                                    }, 0),
                                )}
                            </span>
                        </p>
                    </div>

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

                        {errors.update_payables ||
                            (errors.update_receivables && (
                                <Alert variant="destructive">
                                    <AlertCircleIcon aria-hidden />
                                    <AlertTitle>Unable to process your sale.</AlertTitle>
                                    <AlertDescription>
                                        <ul className="list-inside list-disc text-sm">
                                            {errors.update_payables && <li>{errors.update_payables}</li>}
                                            {errors.update_receivables && <li>{errors.update_receivables}</li>}
                                        </ul>
                                    </AlertDescription>
                                </Alert>
                            ))}
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
                                    setVendorId(null);
                                    setSelectedProducts([]);
                                    setDeliveryDate(null);
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
