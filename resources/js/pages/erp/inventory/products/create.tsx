import InputError from '@/components/common/input-error';
import { ErpPage } from '@/components/erp/page';
import { ProductFields, useProductImages, type ProductFormData } from '@/components/erp/product-fields';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { index as inventory } from '@/routes/erp/inventory';
import products from '@/routes/erp/inventory/products';
import { Form } from '@inertiajs/react';
import { Activity } from 'react';

const title = 'New product';

export default function CreateProduct(props: Readonly<ProductFormData>) {
    const upload = useProductImages(props.images);

    return (
        <ErpPage title={title}>
            <Card className="w-full max-w-3xl">
                <CardContent>
                    <Form
                        {...products.store.form()}
                        transform={upload.transform}
                        className="space-y-5"
                        disableWhileProcessing
                    >
                        {({ errors, processing, progress }) => (
                            <>
                                <ProductFields
                                    data={props}
                                    errors={errors}
                                    upload={upload}
                                    uploadProgress={progress?.percentage}
                                />
                                <InputError role="alert" message={errors['record']} />
                                <Button type="submit" isDisabled={processing}>
                                    <Activity mode={processing ? 'visible' : 'hidden'}>
                                        <Spinner />
                                    </Activity>
                                    Create product
                                </Button>
                            </>
                        )}
                    </Form>
                </CardContent>
            </Card>
        </ErpPage>
    );
}

CreateProduct.layout = (props: { auth: { permissions: string[] } }) => ({
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        ...(props.auth.permissions.includes('product.index') ? [{ title: 'Inventory', href: inventory() }] : []),
        { title, href: products.create() },
    ],
});
