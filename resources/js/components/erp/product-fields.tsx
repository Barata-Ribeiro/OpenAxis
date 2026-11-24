import InputError from '@/components/common/input-error';
import { FileUploadArea, type FileEntry } from '@/components/common/file-upload';
import { Checkbox } from '@/components/ui/checkbox';
import { FieldGroup } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { useId, useState } from 'react';
import {
    CheckboxField,
    CheckboxList,
    fieldError,
    InputField,
    scaleFields,
    scaledInput,
    SelectField,
    TextareaField,
    type Option,
    type FormErrors,
    type FormValues,
} from './form-fields';

type ProductValues = {
    name: string;
    description: string;
    storage_location_id: number | null;
    cost_price_cents: number;
    sale_price_cents: number;
    minimum_stock_milliunits: number;
    is_active: boolean;
    category_ids: number[];
};

export type ProductImage = { id: number; url: string | null };
export type ProductFormData = {
    product: ProductValues | null;
    units: Option[];
    locations: Option[];
    categories: Option[];
    images: ProductImage[];
};

const acceptedImages = ['image/jpeg', 'image/png', 'image/webp'];

export function useProductImages(images: ProductImage[]) {
    const [files, setFiles] = useState<FileEntry[]>([]);
    const [removed, setRemoved] = useState<number[]>([]);
    const [feedback, setFeedback] = useState('');
    const available = Math.max(0, 5 - images.length + removed.length);

    function addFiles(selected: File[]) {
        const valid = selected.filter((file) => acceptedImages.includes(file.type) && file.size <= 2 * 1024 * 1024);
        const additions = valid.slice(0, Math.max(0, available - files.length));
        setFiles([
            ...files,
            ...additions.map((file): FileEntry => ({
                id: crypto.randomUUID(),
                file,
                progress: 0,
                state: 'queued',
            })),
        ]);
        setFeedback(
            valid.length < selected.length
                ? 'Choose JPG, PNG or WebP images up to 2 MB each.'
                : additions.length < selected.length
                  ? 'A product can have at most five images.'
                  : '',
        );
    }

    function toggleRemoval(id: number, selected: boolean) {
        if (!selected && files.length > Math.max(0, available - 1)) {
            setFeedback('Remove a queued image before keeping this image.');
            return;
        }
        setRemoved(selected ? [...removed, id] : removed.filter((item) => item !== id));
        setFeedback('');
    }

    function transform(data: FormValues) {
        return {
            ...scaleFields(data, { cost_price_cents: 2, sale_price_cents: 2, minimum_stock_milliunits: 3 }),
            is_active: data['is_active'] === '1',
            images: files.map(({ file }) => file),
            ...(images.length ? { remove_image_ids: removed } : {}),
        };
    }

    return {
        available,
        files,
        removed,
        feedback,
        addFiles,
        toggleRemoval,
        transform,
        removeFile: (id: string) => {
            setFiles(files.filter((item) => item.id !== id));
            setFeedback('');
        },
    };
}

export function ProductFields({
    data,
    errors,
    upload,
    uploadProgress,
}: Readonly<{
    data: ProductFormData;
    errors: FormErrors;
    upload: ReturnType<typeof useProductImages>;
    uploadProgress?: number;
}>) {
    const id = useId();
    const { product, categories, locations, units, images } = data;
    const files = upload.files.map((entry): FileEntry =>
        uploadProgress === undefined ? entry : { ...entry, state: 'uploading', progress: uploadProgress },
    );

    return (
        <FieldGroup>
            <div className="grid gap-4 sm:grid-cols-2">
                {!product && (
                    <>
                        <InputField name="sku" label="SKU" required maxLength={64} error={errors['sku']} />
                        <SelectField
                            name="unit_of_measure_id"
                            label="Unit of measure"
                            options={units}
                            required
                            error={errors['unit_of_measure_id']}
                        />
                    </>
                )}
                <InputField
                    name="name"
                    label="Name"
                    required
                    maxLength={180}
                    defaultValue={product?.name}
                    error={errors['name']}
                />
                <SelectField
                    name="storage_location_id"
                    label="Storage location"
                    options={locations}
                    defaultValue={product?.storage_location_id}
                    error={errors['storage_location_id']}
                />
                <InputField
                    name="cost_price_cents"
                    label="Reference unit cost (BRL)"
                    type="number"
                    required
                    min={0}
                    step={0.01}
                    defaultValue={scaledInput(product?.cost_price_cents ?? 0, 2)}
                    error={errors['cost_price_cents']}
                />
                <InputField
                    name="sale_price_cents"
                    label="Sale price (BRL)"
                    type="number"
                    required
                    min={0}
                    step={0.01}
                    defaultValue={scaledInput(product?.sale_price_cents ?? 0, 2)}
                    error={errors['sale_price_cents']}
                />
                <InputField
                    name="minimum_stock_milliunits"
                    label="Minimum stock"
                    type="number"
                    required
                    min={0}
                    step={0.001}
                    defaultValue={scaledInput(product?.minimum_stock_milliunits ?? 0, 3)}
                    error={errors['minimum_stock_milliunits']}
                />
                <CheckboxField
                    name="is_active"
                    label="Active"
                    defaultSelected={product?.is_active ?? true}
                    error={errors['is_active']}
                />
            </div>
            <TextareaField
                name="description"
                label="Description"
                defaultValue={product?.description}
                error={errors['description']}
            />
            <CheckboxList
                name="category_ids"
                label="Product categories"
                options={categories}
                selected={product?.category_ids}
                error={fieldError(errors, 'category_ids')}
            />
            {images.length > 0 && (
                <fieldset className="space-y-3">
                    <legend className="text-sm font-medium">Current images</legend>
                    <div className="flex flex-wrap gap-3">
                        {images.map((image, index) => {
                            const checkboxId = id + '-image-' + image.id;
                            const label = 'Remove image ' + (index + 1);
                            return (
                                <div key={image.id} className="w-28 space-y-2">
                                    {image.url && (
                                        <img
                                            className="aspect-square w-full rounded-md border object-cover"
                                            src={image.url}
                                            alt={'Product image ' + (index + 1)}
                                        />
                                    )}
                                    <div className="flex items-center gap-2">
                                        <Checkbox
                                            id={checkboxId}
                                            aria-label={label}
                                            isSelected={upload.removed.includes(image.id)}
                                            onChange={(selected) => upload.toggleRemoval(image.id, selected)}
                                        />
                                        <Label htmlFor={checkboxId}>{label}</Label>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <InputError message={fieldError(errors, 'remove_image_ids')} />
                </fieldset>
            )}
            {upload.available > 0 ? (
                <FileUploadArea
                    title="Product images"
                    description="JPG, PNG or WebP"
                    className="max-w-none rounded-lg"
                    maxFiles={upload.available}
                    maxSizeMB={2}
                    multiple
                    accept={acceptedImages.join(',')}
                    files={files}
                    onFilesSelect={upload.addFiles}
                    onFileRemove={upload.removeFile}
                />
            ) : (
                <p className="text-sm text-muted-foreground">Remove an existing image to upload another.</p>
            )}
            <InputError role="alert" message={upload.feedback || fieldError(errors, 'images')} />
        </FieldGroup>
    );
}
