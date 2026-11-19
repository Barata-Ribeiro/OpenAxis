// Components
import InputError from '@/components/common/input-error';
import TextLink from '@/components/common/text-link';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { Form, Head } from '@inertiajs/react';
import { Activity } from 'react';

export default function ForgotPassword({ status }: Readonly<{ status?: string }>) {
    return (
        <>
            <Head title="Forgot password" />

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}

            <div className="space-y-6">
                <Form
                    {...email.form()}
                    disableWhileProcessing
                    className="inert:pointer-events-none inert:grayscale-100"
                >
                    {({ processing, errors }) => (
                        <>
                            <Field data-invalid={errors['email']}>
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    autoComplete="off"
                                    autoFocus
                                    aria-invalid={!!errors['email']}
                                    placeholder="email@example.com"
                                />

                                <InputError message={errors['email']} />
                            </Field>

                            <div className="my-6 flex items-center justify-start">
                                <Button
                                    type="submit"
                                    className="w-full"
                                    isDisabled={processing}
                                    data-test="email-password-reset-link-button"
                                >
                                    <Activity mode={processing ? 'visible' : 'hidden'}>
                                        <Spinner />
                                    </Activity>
                                    Email password reset link
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="space-x-1 text-center text-sm text-muted-foreground">
                    <span>Or, return to</span>
                    <TextLink href={login()}>log in</TextLink>
                </div>
            </div>
        </>
    );
}

ForgotPassword.layout = {
    title: 'Forgot password',
    description: 'Enter your email to receive a password reset link',
};
