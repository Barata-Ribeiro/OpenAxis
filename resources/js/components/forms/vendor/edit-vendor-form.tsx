import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Spinner } from '@/components/ui/spinner';
import erp from '@/routes/erp';
import type { Vendor } from '@/types/erp/vendor';
import { Form, Link } from '@inertiajs/react';
import { ChevronDownIcon } from 'lucide-react';
import { Activity, useState } from 'react';

export default function EditVendorForm({ vendor }: Readonly<{ vendor: Vendor }>) {
    // State for Date of Birth picker
    const [dateOfBirth, setDateOfBirth] = useState<Date | undefined>(new Date(vendor.date_of_birth ?? ''));
    const [bodOpen, setBodOpen] = useState(false);

    return (
        <Form
            {...erp.vendors.update.form(vendor.id)}
            options={{ preserveScroll: true }}
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            disableWhileProcessing
            transform={(data) => ({
                ...data,
                date_of_birth: dateOfBirth?.toISOString().split('T')[0] ?? null,
            })}
        >
            {({ processing, errors }) => (
                <>
                    <FieldSet>
                        <FieldLegend>Personal Information</FieldLegend>
                        <FieldDescription>
                            Provide the vendor's personal details to create their profile.
                        </FieldDescription>

                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field aria-invalid={!!errors.first_name}>
                                <FieldLabel htmlFor="first_name">First Name</FieldLabel>
                                <Input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    placeholder="e.g. John"
                                    defaultValue={vendor.first_name}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.first_name}
                                />
                                <InputError message={errors.first_name} />
                            </Field>
                            <Field aria-invalid={!!errors.last_name}>
                                <FieldLabel htmlFor="last_name">Last Name</FieldLabel>
                                <Input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    placeholder="e.g. Doe"
                                    defaultValue={vendor.last_name}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.last_name}
                                />
                                <InputError message={errors.last_name} />
                            </Field>
                        </FieldGroup>
                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field data-invalid={!!errors.date_of_birth}>
                                <FieldLabel htmlFor="date_of_birth">Date of Birth</FieldLabel>
                                <Popover open={bodOpen} onOpenChange={setBodOpen}>
                                    <PopoverTrigger asChild>
                                        <Button
                                            variant="outline"
                                            id="date"
                                            className="w-48 justify-between font-normal"
                                        >
                                            {dateOfBirth ? dateOfBirth.toLocaleDateString() : 'Select date'}
                                            <ChevronDownIcon />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                                        <Calendar
                                            mode="single"
                                            selected={dateOfBirth ?? undefined}
                                            captionLayout="dropdown"
                                            showOutsideDays={false}
                                            disabled={(date) => date < new Date('1900-01-01')}
                                            onSelect={(date) => {
                                                setDateOfBirth(date);
                                                setBodOpen(false);
                                            }}
                                        />
                                    </PopoverContent>
                                </Popover>
                                <InputError message={errors.date_of_birth} />
                            </Field>
                            <Field data-invalid={!!errors.phone_number}>
                                <FieldLabel htmlFor="phone_number">Phone Number</FieldLabel>
                                <Input
                                    type="tel"
                                    id="phone_number"
                                    name="phone_number"
                                    placeholder="e.g. +1234567890"
                                    defaultValue={vendor.phone_number}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.phone_number}
                                />
                                <InputError message={errors.phone_number} />
                            </Field>
                        </FieldGroup>
                    </FieldSet>

                    <FieldSet>
                        <FieldLegend>Platform Information</FieldLegend>
                        <FieldDescription>
                            Detail the vendor&apos;s commission rates and activation status.
                        </FieldDescription>

                        <Field data-invalid={!!errors.commission_rate}>
                            <FieldLabel htmlFor="commission_rate">Commission Rate (%)</FieldLabel>

                            <Input
                                type="number"
                                id="commission_rate"
                                name="commission_rate"
                                placeholder="e.g. 15"
                                defaultValue={vendor.commission_rate}
                                min={0}
                                max={100}
                                step={0.01}
                                required
                                aria-required
                                aria-invalid={!!errors.commission_rate}
                            />

                            <InputError message={errors.commission_rate} />
                        </Field>

                        <Field data-invalid={!!errors.is_active}>
                            <div className="flex items-center space-x-3">
                                <Checkbox
                                    id="is_active"
                                    name="is_active"
                                    aria-invalid={!!errors.is_active}
                                    defaultChecked={vendor.is_active}
                                />
                                <FieldLabel htmlFor="is_active">Set as active</FieldLabel>
                            </div>
                            <InputError message={errors.is_active} />
                        </Field>
                    </FieldSet>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.vendors.show(vendor.id)} prefetch="hover" as="button">
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
