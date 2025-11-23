import administrative from '@/routes/administrative';
import { Form, Link } from '@inertiajs/react';

import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { RoleNames, roleLabel } from '@/types/application/enums';
import { Activity } from 'react';

export default function NewUserForm() {
    return (
        <Form
            {...administrative.users.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            resetOnSuccess
            resetOnError={['password']}
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field data-invalid={!!errors.name}>
                        <FieldLabel htmlFor="name">Name</FieldLabel>

                        <Input
                            id="name"
                            name="name"
                            placeholder="Full name"
                            required
                            aria-required
                            aria-invalid={!!errors.name}
                        />

                        <InputError className="mt-2" message={errors.name} />
                    </Field>

                    <Field data-invalid={!!errors.email}>
                        <FieldLabel htmlFor="email">Email address</FieldLabel>

                        <Input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="Email address"
                            required
                            aria-required
                            aria-invalid={!!errors.email}
                        />

                        <InputError className="mt-2" message={errors.email} />
                    </Field>

                    <Field data-invalid={!!errors.password}>
                        <FieldLabel htmlFor="password">
                            Password <span className="text-muted">(Optional)</span>
                        </FieldLabel>

                        <Input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Password"
                            autoComplete="new-password"
                            aria-invalid={!!errors.password}
                        />

                        <InputError className="mt-2" message={errors.password} />
                    </Field>

                    <Field data-invalid={!!errors.role}>
                        <FieldLabel htmlFor="role">Role</FieldLabel>

                        <Select name="role" required aria-required>
                            <SelectTrigger className="w-full" aria-invalid={!!errors.role}>
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

                        <InputError className="mt-2" message={errors.role} />
                    </Field>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={administrative.users.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Create User
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
