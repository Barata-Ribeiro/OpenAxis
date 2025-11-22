import administrative from '@/routes/administrative';
import { Form, Link } from '@inertiajs/react';

import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { RoleNames, roleLabel } from '@/types/application/enums';
import { UserWithRelations } from '@/types/application/user';
import { Activity, useRef } from 'react';

interface EditUserFormProps {
    user: UserWithRelations;
}

export default function EditUserForm({ user }: Readonly<EditUserFormProps>) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const role = user.roles[0]?.name || '';

    return (
        <Form
            {...administrative.users.update.form(user.id)}
            options={{ preserveScroll: true }}
            resetOnError={['password', 'password_confirmation']}
            onError={(errors) => {
                if (errors.password) passwordInput.current?.focus();
            }}
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
                            placeholder="e.g. John Doe"
                            defaultValue={user.name}
                            required
                            aria-required
                            aria-invalid={!!errors.name}
                        />

                        <InputError message={errors.name} />
                    </Field>

                    <Field data-invalid={!!errors.email}>
                        <FieldLabel htmlFor="email">Email address</FieldLabel>

                        <Input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="e.g. john@example.com"
                            defaultValue={user.email}
                            required
                            aria-required
                            aria-invalid={!!errors.email}
                        />

                        <InputError message={errors.email} />
                    </Field>

                    <FieldSet className="border border-dotted p-4">
                        <FieldLegend>Security</FieldLegend>
                        <FieldDescription>
                            Changing the password is optional. Leave these fields blank to keep the user&apos;s current
                            password.
                        </FieldDescription>

                        <FieldGroup>
                            <Field data-invalid={!!errors.password}>
                                <FieldLabel htmlFor="password">
                                    New Password <span className="text-muted">(Optional)</span>
                                </FieldLabel>

                                <Input
                                    ref={passwordInput}
                                    type="password"
                                    id="password"
                                    name="password"
                                    autoComplete="new-password"
                                    placeholder="New password"
                                    aria-invalid={!!errors.password}
                                />

                                <InputError message={errors.password} />
                            </Field>

                            <Field data-invalid={!!errors.password_confirmation}>
                                <FieldLabel htmlFor="password_confirmation">Confirm password</FieldLabel>

                                <Input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    autoComplete="new-password"
                                    placeholder="Confirm password"
                                    aria-invalid={!!errors.password_confirmation}
                                />

                                <InputError message={errors.password_confirmation} />
                            </Field>
                        </FieldGroup>
                    </FieldSet>

                    <Field className="w-fit" data-invalid={!!errors.role}>
                        <FieldLabel htmlFor="role">Role</FieldLabel>

                        <Select name="role" defaultValue={role} required aria-required>
                            <SelectTrigger aria-invalid={!!errors.role}>
                                <SelectValue placeholder="Select role" />
                            </SelectTrigger>

                            <SelectContent>
                                {Object.values(RoleNames).map((r) => (
                                    <SelectItem key={r} value={r}>
                                        {roleLabel(r as RoleNames)}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        <InputError message={errors.role} />
                    </Field>

                    <div className="inline-flex items-center gap-x-2">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={administrative.users.show(user.id)} prefetch="hover" as="button">
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
