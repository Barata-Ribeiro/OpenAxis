import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import erp from '@/routes/erp';
import { addressTypeLabel, AddressTypes } from '@/types/application/enums';
import { partnerTypeLabel, PartnerTypes } from '@/types/erp/erp-enums';
import { type PartnerWithRelations } from '@/types/erp/partner';
import { Form, Link } from '@inertiajs/react';
import { Activity } from 'react';

interface EditSupplierFormProps {
    supplier: PartnerWithRelations;
}

export default function EditSupplierForm({ supplier }: Readonly<EditSupplierFormProps>) {
    const address = supplier.addresses[0];

    return (
        <Form
            {...erp.suppliers.update.form(supplier.id)}
            options={{ preserveScroll: true }}
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            disableWhileProcessing
        >
            {({ processing, errors }) => (
                <>
                    <FieldSet>
                        <FieldLegend>Information</FieldLegend>
                        <FieldDescription>Please provide the supplier's information below.</FieldDescription>

                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field aria-invalid={!!errors.name}>
                                <FieldLabel htmlFor="name">Name</FieldLabel>
                                <Input
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="e.g. John Doe/ACME Corp."
                                    defaultValue={supplier.name}
                                    maxLength={100}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.name}
                                />
                                <InputError message={errors.name} />
                            </Field>
                            <Field aria-invalid={!!errors.email}>
                                <FieldLabel htmlFor="email">Email</FieldLabel>
                                <Input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="e.g. johndoe@example.com"
                                    defaultValue={supplier.email}
                                    maxLength={320}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.email}
                                />
                                <InputError message={errors.email} />
                            </Field>
                        </FieldGroup>

                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field aria-invalid={!!errors.identification}>
                                <FieldLabel htmlFor="identification">Identification</FieldLabel>
                                <FieldDescription>National ID, CPF, SSN, or CNPJ.</FieldDescription>
                                <Input
                                    type="text"
                                    id="identification"
                                    name="identification"
                                    placeholder="e.g. 61031412069"
                                    defaultValue={supplier.identification}
                                    maxLength={50}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.identification}
                                />
                                <InputError message={errors.identification} />
                            </Field>

                            <Field aria-invalid={!!errors.phone_number}>
                                <FieldLabel htmlFor="phone_number">Phone Number</FieldLabel>
                                <FieldDescription>Include country and area codes.</FieldDescription>

                                <Input
                                    type="text"
                                    id="phone_number"
                                    name="phone_number"
                                    placeholder="e.g. +1 (555) 123-4567"
                                    defaultValue={supplier.phone_number}
                                    maxLength={20}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.phone_number}
                                />

                                <InputError message={errors.phone_number} />
                            </Field>
                        </FieldGroup>

                        <Field data-invalid={!!errors.type}>
                            <FieldLabel htmlFor="type">Supplier Type</FieldLabel>
                            <Select name="type" defaultValue={supplier.type} required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.type}>
                                    <SelectValue placeholder="Select a type" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(PartnerTypes)
                                        .filter((type) => type !== PartnerTypes.CLIENT)
                                        .map((type) => (
                                            <SelectItem key={type} value={type}>
                                                {partnerTypeLabel(type)}
                                            </SelectItem>
                                        ))}
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldSet>

                    <FieldSet>
                        <FieldLegend>Address Information</FieldLegend>
                        <FieldDescription>Please provide the supplier's address information below.</FieldDescription>

                        <Field data-invalid={!!errors.type}>
                            <FieldLabel htmlFor="type">Address type</FieldLabel>

                            <Select name="type" defaultValue={address.type} required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.type}>
                                    <SelectValue placeholder="Select address type" />
                                </SelectTrigger>

                                <SelectContent>
                                    {Object.values(AddressTypes).map((t) => (
                                        <SelectItem key={t} value={t}>
                                            {addressTypeLabel(t as AddressTypes)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            <InputError message={errors.type} />
                        </Field>

                        <Field data-invalid={!!errors.label}>
                            <FieldLabel htmlFor="label">Label</FieldLabel>
                            <Input
                                id="label"
                                name="label"
                                placeholder="Home, Office, etc."
                                defaultValue={address.label ?? ''}
                            />
                            <InputError message={errors.label} />
                        </Field>

                        <Field data-invalid={!!errors.street}>
                            <FieldLabel htmlFor="street">Street</FieldLabel>
                            <Input
                                id="street"
                                name="street"
                                placeholder="Street name"
                                defaultValue={address.street}
                                required
                                aria-required
                                aria-invalid={!!errors.street}
                            />
                            <InputError message={errors.street} />
                        </Field>

                        <FieldGroup className="grid gap-4 sm:grid-cols-2">
                            <Field data-invalid={!!errors.number}>
                                <FieldLabel htmlFor="number">Number</FieldLabel>
                                <Input
                                    id="number"
                                    name="number"
                                    placeholder="123"
                                    defaultValue={address.number}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.number}
                                />
                                <InputError message={errors.number} />
                            </Field>

                            <Field data-invalid={!!errors.complement}>
                                <FieldLabel htmlFor="complement">Complement</FieldLabel>
                                <Input
                                    id="complement"
                                    name="complement"
                                    placeholder="Apartment, suite, etc."
                                    defaultValue={address.complement}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.complement}
                                />
                                <InputError message={errors.complement} />
                            </Field>
                        </FieldGroup>

                        <Field data-invalid={!!errors.neighborhood}>
                            <FieldLabel htmlFor="neighborhood">Neighborhood</FieldLabel>
                            <Input
                                id="neighborhood"
                                name="neighborhood"
                                placeholder="Neighborhood"
                                defaultValue={address.neighborhood}
                                required
                                aria-required
                                aria-invalid={!!errors.neighborhood}
                            />
                            <InputError message={errors.neighborhood} />
                        </Field>

                        <FieldGroup className="grid gap-4 sm:grid-cols-3">
                            <Field data-invalid={!!errors.city}>
                                <FieldLabel htmlFor="city">City</FieldLabel>
                                <Input
                                    id="city"
                                    name="city"
                                    placeholder="City"
                                    defaultValue={address.city}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.city}
                                />
                                <InputError message={errors.city} />
                            </Field>

                            <Field data-invalid={!!errors.state}>
                                <FieldLabel htmlFor="state">State</FieldLabel>
                                <Input
                                    id="state"
                                    name="state"
                                    placeholder="State"
                                    defaultValue={address.state}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.state}
                                />
                                <InputError message={errors.state} />
                            </Field>

                            <Field data-invalid={!!errors.postal_code}>
                                <FieldLabel htmlFor="postal_code">Postal code</FieldLabel>
                                <Input
                                    id="postal_code"
                                    name="postal_code"
                                    placeholder="Postal code"
                                    defaultValue={address.postal_code}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.postal_code}
                                />
                                <InputError message={errors.postal_code} />
                            </Field>
                        </FieldGroup>

                        <Field data-invalid={!!errors.country}>
                            <FieldLabel htmlFor="country">Country</FieldLabel>
                            <Input
                                id="country"
                                name="country"
                                placeholder="Country"
                                defaultValue={address.country}
                                required
                                aria-required
                                aria-invalid={!!errors.country}
                            />
                            <InputError message={errors.country} />
                        </Field>

                        <Field data-invalid={!!errors.is_primary}>
                            <div className="flex items-center space-x-3">
                                <Checkbox id="is_primary" name="is_primary" checked disabled aria-disabled />
                                <FieldLabel htmlFor="is_primary">Set as primary address</FieldLabel>
                            </div>
                            <InputError message={errors.is_primary} />
                        </Field>
                    </FieldSet>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.suppliers.show(supplier.id)} prefetch="hover" as="button">
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
