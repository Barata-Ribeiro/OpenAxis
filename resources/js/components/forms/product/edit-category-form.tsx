import erp from '@/routes/erp';
import { Form, Link } from '@inertiajs/react';

import InputError from '@/components/feedback/input-error';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { ProductCategory } from '@/types/erp/product-category';
import { Activity } from 'react';

interface EditCategoryFormProps {
    category: ProductCategory;
}

export default function EditCategoryForm({ category }: Readonly<EditCategoryFormProps>) {
    return (
        <Form
            {...erp.categories.update.form(category.slug)}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, errors }) => (
                <>
                    <Field data-invalid={!!errors.name}>
                        <FieldLabel htmlFor="name">Name</FieldLabel>

                        <Input
                            id="name"
                            name="name"
                            placeholder="e.g. Electronics, Furniture"
                            defaultValue={category.name}
                            required
                            aria-required
                            aria-invalid={!!errors.name}
                        />

                        <InputError message={errors.name} />
                    </Field>

                    <Field data-invalid={!!errors.description}>
                        <FieldLabel htmlFor="description">Description</FieldLabel>

                        <Textarea
                            id="description"
                            name="description"
                            placeholder="Type a brief description for the category..."
                            defaultValue={category.description}
                            required
                            aria-required
                            aria-invalid={!!errors.description}
                        />

                        <InputError message={errors.description} />
                    </Field>

                    <Field data-invalid={!!errors.is_active}>
                        <div className="flex items-center gap-3">
                            <Checkbox id="is_active" name="is_active" defaultChecked={category.is_active} />
                            <FieldLabel htmlFor="is_active">Set as active</FieldLabel>
                        </div>

                        <InputError message={errors.is_active} />
                    </Field>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.categories.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>

                        <Button type="submit" disabled={processing}>
                            <Activity mode={processing ? 'visible' : 'hidden'}>
                                <Spinner />
                            </Activity>
                            Save
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
