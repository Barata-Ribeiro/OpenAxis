import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import HeadingSmall from '@/components/common/heading-small';
import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit } from '@/routes/user-password';
import type { BreadcrumbItem } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head } from '@inertiajs/react';
import { Activity, useRef } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: edit().url,
    },
];

export default function Password() {
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Password settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Update password"
                        description="Ensure your account is using a long, random password to stay secure"
                    />

                    <Form
                        {...PasswordController.update.form()}
                        options={{ preserveScroll: true }}
                        resetOnError={['password', 'password_confirmation', 'current_password']}
                        resetOnSuccess
                        onError={(errors) => {
                            if (errors.password) {
                                passwordInput.current?.focus();
                            }

                            if (errors.current_password) {
                                currentPasswordInput.current?.focus();
                            }
                        }}
                        disableWhileProcessing
                        className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
                    >
                        {({ errors, processing, recentlySuccessful }) => (
                            <>
                                <Field data-invalid={!!errors.current_password}>
                                    <FieldLabel htmlFor="current_password">Current password</FieldLabel>

                                    <Input
                                        ref={currentPasswordInput}
                                        type="password"
                                        id="current_password"
                                        name="current_password"
                                        autoComplete="current-password"
                                        placeholder="Current password"
                                        required
                                        aria-required
                                        aria-invalid={!!errors.current_password}
                                    />

                                    <InputError message={errors.current_password} />
                                </Field>

                                <Field data-invalid={!!errors.password}>
                                    <FieldLabel htmlFor="password">New password</FieldLabel>

                                    <Input
                                        ref={passwordInput}
                                        type="password"
                                        id="password"
                                        name="password"
                                        autoComplete="new-password"
                                        placeholder="New password"
                                        required
                                        aria-required
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
                                        required
                                        aria-required
                                        aria-invalid={!!errors.password_confirmation}
                                    />

                                    <InputError message={errors.password_confirmation} />
                                </Field>

                                <div className="flex items-center gap-4">
                                    <Button disabled={processing} data-test="update-password-button">
                                        <Activity mode={processing ? 'visible' : 'hidden'}>
                                            <Spinner />
                                        </Activity>
                                        Save password
                                    </Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">Saved</p>
                                    </Transition>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
