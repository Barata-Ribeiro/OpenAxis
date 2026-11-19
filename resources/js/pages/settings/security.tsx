import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/common/heading';
import InputError from '@/components/common/input-error';
import PasswordInput from '@/components/common/password-input';
import type { Props as ManagePasskeysProps } from '@/components/helpers/passkey/manage-passkeys';
import ManagePasskeys from '@/components/helpers/passkey/manage-passkeys';
import type { Props as ManageTwoFactorProps } from '@/components/helpers/two-factor/manage-two-factor';
import ManageTwoFactor from '@/components/helpers/two-factor/manage-two-factor';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { edit } from '@/routes/security';
import { Form, Head } from '@inertiajs/react';
import { Activity, useRef } from 'react';

// oxfmt-ignore
type Props = {
    passwordRules: string;
} & ManagePasskeysProps &
    ManageTwoFactorProps;

export default function Security(props: Props) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    return (
        <>
            <Head title="Security settings" />

            <h1 className="sr-only">Security settings</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Update password"
                    description="Ensure your account is using a long, random password to stay secure"
                />

                <Form
                    {...SecurityController.update.form()}
                    options={{ preserveScroll: true }}
                    resetOnError={['password', 'password_confirmation', 'current_password']}
                    resetOnSuccess
                    onError={(errors) => {
                        if (errors['password']) {
                            passwordInput.current?.focus();
                        }

                        if (errors['current_password']) {
                            currentPasswordInput.current?.focus();
                        }
                    }}
                    disableWhileProcessing
                    className="space-y-6 inert:pointer-events-none inert:grayscale-100"
                >
                    {({ errors, processing }) => (
                        <>
                            <Field data-invalid={!!errors['current_password']}>
                                <Label htmlFor="current_password">Current password</Label>

                                <PasswordInput
                                    id="current_password"
                                    ref={currentPasswordInput}
                                    name="current_password"
                                    className="mt-1 block w-full"
                                    autoComplete="current-password"
                                    placeholder="Current password"
                                    aria-invalid={!!errors['current_password']}
                                />

                                <InputError message={errors['current_password']} />
                            </Field>

                            <Field data-invalid={!!errors['password']} className="grid gap-2">
                                <Label htmlFor="password">New password</Label>

                                <PasswordInput
                                    id="password"
                                    ref={passwordInput}
                                    name="password"
                                    className="mt-1 block w-full"
                                    autoComplete="new-password"
                                    placeholder="New password"
                                    aria-invalid={!!errors['password']}
                                    passwordrules={props.passwordRules}
                                />

                                <InputError message={errors['password']} />
                            </Field>

                            <Field data-invalid={!!errors['password_confirmation']} className="grid gap-2">
                                <Label htmlFor="password_confirmation">Confirm password</Label>

                                <PasswordInput
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    className="mt-1 block w-full"
                                    autoComplete="new-password"
                                    placeholder="Confirm password"
                                    aria-invalid={!!errors['password_confirmation']}
                                    passwordrules={props.passwordRules}
                                />

                                <InputError message={errors['password_confirmation']} />
                            </Field>

                            <div className="flex items-center gap-4">
                                <Button type="submit" isDisabled={processing} data-test="update-password-button">
                                    <Activity mode={processing ? 'visible' : 'hidden'}>
                                        <Spinner aria-hidden />
                                    </Activity>
                                    Save
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>

            <ManageTwoFactor
                canManageTwoFactor={props.canManageTwoFactor}
                requiresConfirmation={props.requiresConfirmation}
                twoFactorEnabled={props.twoFactorEnabled}
            />

            <ManagePasskeys canManagePasskeys={props.canManagePasskeys} passkeys={props.passkeys} />
        </>
    );
}

Security.layout = {
    breadcrumbs: [
        {
            title: 'Security settings',
            href: edit(),
        },
    ],
};
