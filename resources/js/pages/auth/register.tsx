import InputError from '@/components/common/input-error';
import PasswordInput from '@/components/common/password-input';
import TextLink from '@/components/common/text-link';
import { Button } from '@/components/ui/button';
import { Field, FieldGroup } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/react';
import { Activity } from 'react';

type Props = {
    passwordRules: string;
};

export default function Register({ passwordRules }: Readonly<Props>) {
    return (
        <>
            <Head title="Register" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6 inert:pointer-events-none inert:grayscale-100"
            >
                {({ processing, errors }) => (
                    <>
                        <FieldGroup>
                            <Field data-invalid={!!errors['name']}>
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Full name"
                                    aria-invalid={!!errors['name']}
                                />
                                <InputError message={errors['name']} className="mt-2" />
                            </Field>

                            <Field data-invalid={!!errors['email']}>
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                    aria-invalid={!!errors['email']}
                                />
                                <InputError message={errors['email']} />
                            </Field>

                            <Field data-invalid={!!errors['password']} className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <PasswordInput
                                    id="password"
                                    required
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Password"
                                    passwordrules={passwordRules}
                                    aria-invalid={!!errors['password']}
                                />
                                <InputError message={errors['password']} />
                            </Field>

                            <Field data-invalid={!!errors['password_confirmation']} className="grid gap-2">
                                <Label htmlFor="password_confirmation">Confirm password</Label>
                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                    passwordrules={passwordRules}
                                    aria-invalid={!!errors['password_confirmation']}
                                />
                                <InputError message={errors['password_confirmation']} />
                            </Field>

                            <Button type="submit" className="mt-2 w-full" data-test="register-user-button">
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Create account
                            </Button>
                        </FieldGroup>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account? <TextLink href={login()}>Log in</TextLink>
                        </div>
                    </>
                )}
            </Form>
        </>
    );
}

Register.layout = {
    title: 'Create an account',
    description: 'Enter your details below to create your account',
};
