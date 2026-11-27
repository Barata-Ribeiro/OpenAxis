import InputError from '@/components/common/input-error';
import { RecordScreen } from '@/components/erp/records/record-screen';
import type { RecordScreenProps } from '@/components/erp/records/record-screen';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useProductImages } from '@/hooks/erp/use-product-images';
import type { FormTarget, FormValues } from '@/types/erp/forms';
import type { ProductFormData } from '@/types/erp/products';
import { Form } from '@inertiajs/react';

import { ProductFields } from './product-fields';

export function ProductEditor({
    data,
    target,
    ...props
}: Readonly<
    RecordScreenProps & {
        data: ProductFormData;
        target: FormTarget;
    }
>) {
    const upload = useProductImages(data.images);
    const editing = data.product !== null;

    function transform(values: FormValues) {
        const formData = upload.transform(values);
        return editing ? { ...formData, _method: 'PATCH' } : formData;
    }

    return (
        <RecordScreen {...props} description="Complete the product fields">
            {({ close, setProcessing }) => (
                <Form
                    {...target}
                    transform={transform}
                    className="space-y-5"
                    disableWhileProcessing
                    options={{ preserveScroll: true }}
                    onStart={() => setProcessing(true)}
                    onFinish={() => setProcessing(false)}
                    onSuccess={close}
                >
                    {({ errors, processing, progress }) => (
                        <>
                            <ProductFields
                                data={data}
                                errors={errors}
                                upload={upload}
                                uploadProgress={progress?.percentage}
                            />
                            <InputError role="alert" message={errors['record']} />
                            <Button type="submit" isDisabled={processing}>
                                {processing && <Spinner />}
                                {editing ? 'Save' : 'Create product'}
                            </Button>
                        </>
                    )}
                </Form>
            )}
        </RecordScreen>
    );
}
