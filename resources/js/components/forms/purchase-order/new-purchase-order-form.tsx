import InputError from '@/components/feedback/input-error';
import CalendarDatePicker from '@/components/helpers/calendar-date-picker';
import PartnerSelectCombobox from '@/components/helpers/partners/partner-select-combobox';
import ItemsForPurchaseOrder, {
    type SelectedProduct,
} from '@/components/helpers/purchase-order/items-for-purchase-order';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { formatCurrency } from '@/lib/utils';
import erp from '@/routes/erp';
import { PurchaseOrderStatus, purchaseOrderStatusLabel } from '@/types/erp/erp-enums';
import { Form, Link } from '@inertiajs/react';
import { PlusCircleIcon } from 'lucide-react';
import { Activity, useState } from 'react';

export default function NewPurchaseOrderForm() {
    const [supplierId, setSupplierId] = useState<number | null>(null);
    const [selectedProducts, setSelectedProducts] = useState<SelectedProduct[]>([]);
    const [orderDate, setOrderDate] = useState<Date | null>(null);
    const [forecastDate, setForecastDate] = useState<Date | null>(null);

    return (
        <Form
            {...erp.purchaseOrders.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                supplier_id: supplierId,
                items: selectedProducts.map((product) => ({
                    product_id: product.id,
                    quantity: product.quantity,
                    unit_price: product.selling_price,
                    subtotal_price: Number(product.selling_price) * Number(product.quantity),
                })),
                order_date: orderDate ? orderDate.toISOString().split('T')[0] : null,
                forecast_date: forecastDate ? forecastDate.toISOString().split('T')[0] : null,
            })}
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field aria-invalid={!!errors.supplier_id}>
                        <FieldLabel htmlFor="supplier_id">Supplier</FieldLabel>

                        <ButtonGroup className="w-full">
                            <PartnerSelectCombobox
                                value={supplierId}
                                setValue={setSupplierId}
                                route={erp.purchaseOrders.create()}
                            />
                            <Button
                                variant="outline"
                                aria-label="Register a new supplier"
                                title="Register a new supplier"
                                asChild
                            >
                                <Link href={erp.suppliers.create()} prefetch="hover" as="button">
                                    <PlusCircleIcon aria-hidden />
                                </Link>
                            </Button>
                        </ButtonGroup>
                        <InputError message={errors.supplier_id} />
                    </Field>

                    <FieldGroup className="grid gap-4 sm:grid-cols-2">
                        <Field aria-invalid={!!errors.order_date}>
                            <FieldLabel htmlFor="order_date">Order Date</FieldLabel>
                            <CalendarDatePicker
                                value={orderDate}
                                setValue={setOrderDate}
                                limitToCurrentYearAndFuture={false}
                            />
                            <InputError message={errors.order_date} />
                        </Field>

                        <Field aria-invalid={!!errors.forecast_date}>
                            <FieldLabel htmlFor="forecast_date">Forecast Date</FieldLabel>
                            <CalendarDatePicker value={forecastDate} setValue={setForecastDate} />
                            <InputError message={errors.forecast_date} />
                        </Field>
                    </FieldGroup>

                    <Field data-invalid={!!errors.status}>
                        <FieldLabel htmlFor="status">Purchase Status</FieldLabel>
                        <Select name="status" required aria-required>
                            <SelectTrigger className="w-full" aria-invalid={!!errors.status}>
                                <SelectValue placeholder="Select a status" />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.values(PurchaseOrderStatus).map((status) => (
                                    <SelectItem key={status} value={status}>
                                        {purchaseOrderStatusLabel(status)}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </Field>

                    <Field aria-invalid={!!errors.notes}>
                        <FieldLabel htmlFor="notes">Notes</FieldLabel>
                        <Textarea id="notes" name="notes" rows={4} />
                        <InputError message={errors.notes} />
                    </Field>

                    <ItemsForPurchaseOrder
                        value={selectedProducts}
                        setValue={setSelectedProducts}
                        route={erp.purchaseOrders.create()}
                        errors={errors.items}
                    />

                    <p className="text-right text-3xl">
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
                                onClick={() => {
                                    resetAndClearErrors();
                                    setSupplierId(null);
                                    setSelectedProducts([]);
                                    setOrderDate(null);
                                    setForecastDate(null);
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
