import { ProductEditor } from '@/components/erp/inventory/products/product-editor';
import type { RecordScreenProps } from '@/components/erp/records/record-screen';
import { recordLayout } from '@/lib/erp/navigation';
import { index as inventory } from '@/routes/erp/inventory';
import products from '@/routes/erp/inventory/products';
import type { ProductFormData } from '@/types/erp/products';

export default function EditProduct({
    id,
    productForm,
    ...props
}: Readonly<RecordScreenProps & { id: number; productForm: ProductFormData }>) {
    return <ProductEditor {...props} data={productForm} target={{ action: products.update.url(id), method: 'post' }} />;
}

EditProduct.layout = recordLayout({ title: 'Inventory', href: inventory() }, 'product.index');
