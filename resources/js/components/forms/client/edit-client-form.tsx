import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import erp from '@/routes/erp';
import type { Client } from '@/types/erp/client';
import { ClientType, clientTypeLabel } from '@/types/erp/erp-enums';
import { Form, Link } from '@inertiajs/react';
import { Activity } from 'react';

export default function NewClientForm({ client }: Readonly<{ client: Client }>) {
    return (
        <Form
            {...erp.clients.update.form(client.id)}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, errors }) => (
                <>
                    <FieldSet>
                        <FieldLegend>Information</FieldLegend>
                        <FieldDescription>
                            Adjust the client&apos;s information as necessary. Ensure all required fields are filled out
                            accurately.
                        </FieldDescription>

                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field aria-invalid={!!errors.name}>
                                <FieldLabel htmlFor="name">Name</FieldLabel>
                                <Input
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="e.g. John Doe"
                                    defaultValue={client.name}
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
                                    defaultValue={client.email}
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
                                    defaultValue={client.identification}
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
                                    defaultValue={client.phone_number}
                                    maxLength={20}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.phone_number}
                                />

                                <InputError message={errors.phone_number} />
                            </Field>
                        </FieldGroup>

                        <Field data-invalid={!!errors.client_type}>
                            <FieldLabel htmlFor="client_type">Client Type</FieldLabel>
                            <Select name="client_type" defaultValue={client.client_type} required aria-required>
                                <SelectTrigger className="w-full" aria-invalid={!!errors.client_type}>
                                    <SelectValue placeholder="Select a type" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(ClientType).map((type) => (
                                        <SelectItem key={type} value={type}>
                                            {clientTypeLabel(type)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldSet>

                    <p className="text-sm text-muted-foreground">
                        Please, review the information provided before submitting the form to ensure accuracy.
                    </p>

                    <div className="inline-flex items-center gap-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.clients.index()} prefetch="hover" as="button">
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
