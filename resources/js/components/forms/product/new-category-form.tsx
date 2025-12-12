import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import erp from '@/routes/erp';
import { Form, Link } from '@inertiajs/react';
import { Activity } from 'react';

export default function NewCategoryForm() {
    return (
        <Form
            {...erp.categories.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field data-invalid={!!errors.name}>
                        <FieldLabel htmlFor="name">Name</FieldLabel>

                        <Input
                            id="name"
                            name="name"
                            placeholder="e.g. Electronics, Furniture"
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
                            required
                            aria-required
                            aria-invalid={!!errors.description}
                        />

                        <InputError message={errors.description} />
                    </Field>

                    <Field data-invalid={!!errors.is_active}>
                        <div className="flex items-center gap-3">
                            <Checkbox id="is_active" name="is_active" />
                            <FieldLabel htmlFor="is_active">Set as active</FieldLabel>
                        </div>

                        <InputError message={errors.is_active} />
                    </Field>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.categories.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Create Category
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => resetAndClearErrors()}
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
