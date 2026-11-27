import { ProductEditor } from '@/components/erp/inventory/products/product-editor';
import { recordLayout } from '@/lib/erp/navigation';
import { index as inventory } from '@/routes/erp/inventory';
import products from '@/routes/erp/inventory/products';
import type { ProductFormData } from '@/types/erp/products';

const title = 'New product';

export default function CreateProduct(data: Readonly<ProductFormData>) {
    return <ProductEditor title={title} data={data} target={products.store.form()} />;
}

const layout = recordLayout({ title: 'Inventory', href: inventory() }, 'product.index');

CreateProduct.layout = (props: Omit<Parameters<typeof layout>[0], 'title'>) => layout({ ...props, title });
