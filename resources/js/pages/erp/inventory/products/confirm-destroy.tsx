import { ConfirmRecord } from '@/components/erp/records/confirm-record';
import type { ConfirmRecordProps } from '@/components/erp/records/confirm-record';
import { recordLayout } from '@/lib/erp/navigation';
import { index as inventory } from '@/routes/erp/inventory';
import products from '@/routes/erp/inventory/products';

export default function ConfirmDestroyProduct({ id, ...props }: Readonly<ConfirmRecordProps>) {
    return (
        <ConfirmRecord
            {...props}
            target={products.destroy.form(id)}
            prompt="Archive this product?"
            confirmLabel="Archive"
        />
    );
}

ConfirmDestroyProduct.layout = recordLayout({ title: 'Inventory', href: inventory() }, 'product.index');
