import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import profile from '@/routes/profile';
import type { Address } from '@/types/application/address';
import { AddressTypes, addressTypeLabel } from '@/types/application/enums';
import { Form } from '@inertiajs/react';
import { Activity, type Dispatch, type SetStateAction } from 'react';

export default function EditAddressModal(
    props: Readonly<{
        address: Address;
        isOpen: boolean;
        setIsOpen: Dispatch<SetStateAction<boolean>>;
    }>,
) {
    return (
        <Dialog open={props.isOpen} onOpenChange={props.setIsOpen}>
            <DialogContent className="max-sm:max-h-11/12 max-sm:overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Edit Address</DialogTitle>
                    <DialogDescription>
                        Make changes to your address information below and click save when you're done.
                    </DialogDescription>
                </DialogHeader>

                <Form
                    {...profile.addresses.update.form(props.address.id)}
                    options={{
                        preserveScroll: true,
                    }}
                    onSuccess={() => props.setIsOpen(false)}
                    className="space-y-4 transition inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
                    disableWhileProcessing
                >
                    {({ errors, processing }) => (
                        <>
                            <FieldGroup>
                                <Field data-invalid={!!errors.type}>
                                    <FieldLabel htmlFor="type">Address type</FieldLabel>
                                    <Select name="type" required aria-required defaultValue={props.address.type}>
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
                                        defaultValue={props.address.label ?? ''}
                                        aria-invalid={!!errors.label}
                                    />
                                    <InputError message={errors.label} />
                                </Field>

                                <Field data-invalid={!!errors.street}>
                                    <FieldLabel htmlFor="street">Street</FieldLabel>
                                    <Input
                                        id="street"
                                        name="street"
                                        placeholder="Street name"
                                        defaultValue={props.address.street}
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
                                            defaultValue={props.address.number}
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
                                            defaultValue={props.address.complement}
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
                                        defaultValue={props.address.neighborhood}
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
                                            defaultValue={props.address.city}
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
                                            defaultValue={props.address.state}
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
                                            defaultValue={props.address.postal_code}
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
                                        defaultValue={props.address.country}
                                        required
                                        aria-required
                                        aria-invalid={!!errors.country}
                                    />
                                    <InputError message={errors.country} />
                                </Field>

                                <Field data-invalid={!!errors.is_primary}>
                                    <div className="flex items-center space-x-3">
                                        <Checkbox
                                            id="is_primary"
                                            name="is_primary"
                                            defaultChecked={props.address.is_primary}
                                        />
                                        <Label htmlFor="is_primary">Set as primary address</Label>
                                    </div>
                                    <InputError message={errors.is_primary} />
                                </Field>
                            </FieldGroup>

                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button type="button" variant="outline">
                                        Cancel
                                    </Button>
                                </DialogClose>

                                <Button type="submit" disabled={processing}>
                                    <Activity mode={processing ? 'visible' : 'hidden'}>
                                        <Spinner />
                                    </Activity>
                                    Save
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
