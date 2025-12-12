import InputError from '@/components/feedback/input-error';
import SortableImageUpload, { type ImageFile } from '@/components/sortable';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import erp from '@/routes/erp';
import { Form, Link } from '@inertiajs/react';
import { AlertCircleIcon } from 'lucide-react';
import { Activity, useState } from 'react';

export default function NewProductForm({ categories }: Readonly<{ categories: string[] }>) {
    const [productImages, setProductImages] = useState<ImageFile[]>([]);
    const [resetKey, setResetKey] = useState(0);

    const handleRemoveImage = (id: string) => {
        setProductImages((prev) => prev.filter((img) => img.id !== id));
    };

    return (
        <Form
            {...erp.products.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                images: productImages.map((img) => img.file),
            })}
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field aria-invalid={!!errors.sku}>
                        <FieldLabel htmlFor="sku">SKU</FieldLabel>

                        <Input
                            type="text"
                            id="sku"
                            name="sku"
                            placeholder="e.g. TSBLMA101"
                            maxLength={50}
                            required
                            aria-required
                            aria-invalid={!!errors.sku}
                        />

                        <InputError message={errors.sku} />
                        <FieldDescription>
                            A unique identifier for the product, typically alphanumeric. Ex:
                            TS(T-Shirt)-BL(Blue)M(Medium)-A101(Brand/Style Code)
                        </FieldDescription>
                    </Field>

                    <Field aria-invalid={!!errors.name}>
                        <FieldLabel htmlFor="name">Product Name</FieldLabel>

                        <Input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="e.g. T-Shirt"
                            maxLength={100}
                            required
                            aria-required
                            aria-invalid={!!errors.name}
                        />
                        <InputError message={errors.name} />
                    </Field>

                    <Field aria-invalid={!!errors.description}>
                        <FieldLabel htmlFor="description">Product Description</FieldLabel>

                        <Textarea
                            id="description"
                            name="description"
                            placeholder="Type the product description here..."
                            aria-invalid={!!errors.description}
                        />

                        <InputError message={errors.description} />
                    </Field>

                    {/* Pricing Information */}
                    <FieldSet className="border p-4">
                        <FieldLegend>Pricing Information</FieldLegend>
                        <FieldDescription>Specify the cost price and selling price for the product.</FieldDescription>

                        <FieldGroup className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <Field data-invalid={!!errors.cost_price}>
                                <FieldLabel htmlFor="cost_price">Cost Price</FieldLabel>

                                <Input
                                    type="number"
                                    id="cost_price"
                                    name="cost_price"
                                    placeholder="e.g. 10.00"
                                    step="0.01"
                                    min="0"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.cost_price}
                                />

                                <InputError message={errors.cost_price} />
                            </Field>

                            <Field data-invalid={!!errors.selling_price}>
                                <FieldLabel htmlFor="selling_price">Selling Price</FieldLabel>

                                <Input
                                    type="number"
                                    id="selling_price"
                                    name="selling_price"
                                    placeholder="e.g. 15.00"
                                    step="0.01"
                                    min="0"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.selling_price}
                                />

                                <InputError message={errors.selling_price} />
                            </Field>

                            <Field className="md:col-span-2" data-invalid={!!errors.comission}>
                                <FieldLabel htmlFor="comission">Comission (%)</FieldLabel>

                                <Input
                                    type="number"
                                    id="comission"
                                    name="comission"
                                    placeholder="e.g. 5"
                                    step="1"
                                    min="0"
                                    max="100"
                                    aria-invalid={!!errors.comission}
                                />

                                <InputError message={errors.comission} />
                            </Field>
                        </FieldGroup>
                    </FieldSet>

                    <FieldSet className="border p-4">
                        <FieldLegend>Stock Information</FieldLegend>
                        <FieldDescription>
                            Specify the minimum stock quantity and current stock level for the product.
                        </FieldDescription>

                        <FieldGroup className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <Field data-invalid={!!errors.minimum_stock}>
                                <FieldLabel htmlFor="minimum_stock">Minimum Stock</FieldLabel>

                                <Input
                                    type="number"
                                    id="minimum_stock"
                                    name="minimum_stock"
                                    placeholder="e.g. 50"
                                    min="0"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.minimum_stock}
                                />

                                <InputError message={errors.minimum_stock} />
                            </Field>

                            <Field data-invalid={!!errors.current_stock}>
                                <FieldLabel htmlFor="current_stock">Current Stock</FieldLabel>

                                <Input
                                    type="number"
                                    id="current_stock"
                                    name="current_stock"
                                    placeholder="e.g. 100"
                                    min="0"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.current_stock}
                                />

                                <InputError message={errors.current_stock} />
                            </Field>
                        </FieldGroup>
                    </FieldSet>

                    <FieldGroup className="grid grid-cols-1 items-center gap-4 md:grid-cols-2">
                        <Field data-invalid={!!errors.is_active}>
                            <div className="flex items-center gap-3">
                                <Checkbox id="is_active" name="is_active" />
                                <FieldLabel htmlFor="is_active">Set product as active</FieldLabel>
                            </div>

                            <InputError message={errors.is_active} />
                        </Field>

                        <Field data-invalid={!!errors.category}>
                            <FieldLabel htmlFor="category">Category</FieldLabel>

                            <Select name="category" required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.category}>
                                    <SelectValue placeholder="Select a category" />
                                </SelectTrigger>
                                <SelectContent>
                                    {categories.map((category) => (
                                        <SelectItem key={category} value={category}>
                                            {category}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldGroup>

                    <Separator />

                    {errors.images && (
                        <Alert variant="destructive" className="border-red-500 bg-red-50">
                            <AlertCircleIcon />
                            <AlertTitle>Error!</AlertTitle>
                            <AlertDescription>{errors.images}</AlertDescription>
                        </Alert>
                    )}

                    <SortableImageUpload
                        maxSize={5 * 1024 * 1024}
                        maxFiles={3}
                        value={productImages}
                        onImagesChange={setProductImages}
                        onOrderChange={setProductImages}
                        onRemoveImage={handleRemoveImage}
                        resetKey={resetKey}
                    />

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.products.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                New Product
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    resetAndClearErrors();
                                    setProductImages([]);
                                    setResetKey((k) => k + 1);
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
