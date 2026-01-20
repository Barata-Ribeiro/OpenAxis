import erp from '@/routes/erp';
import type { SaleOrderWithRelations } from '@/types/erp/sale-order';
import { Form } from '@inertiajs/react';

interface EditSalesOrderFormProps {
    saleOrder: SaleOrderWithRelations;
}

export default function EditSalesOrderForm({ saleOrder }: Readonly<EditSalesOrderFormProps>) {
    return (
        <Form
            {...erp.salesOrders.update.form(saleOrder.id)}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                // TODO: Transform data before submit
            })}
        >
            {({ processing, errors }) => <>{/* TODO: Implement edit sales order form */}</>}
        </Form>
    );
}
