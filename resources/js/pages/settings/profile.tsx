import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/common/heading';
import InputError from '@/components/common/input-error';
import DeleteUser from '@/components/helpers/delete-user';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { Auth } from '@/types';
import { Form, Head, Link, usePage } from '@inertiajs/react';
import { Activity } from 'react';

type PageProps = {
    auth: Auth;
};

export default function Profile({ mustVerifyEmail, status }: Readonly<{ mustVerifyEmail: boolean; status?: string }>) {
    const { auth } = usePage<PageProps>().props;

    return (
        <>
            <Head title="Profile settings" />

            <h1 className="sr-only">Profile settings</h1>

            <div className="space-y-6">
                <Heading variant="small" title="Profile" description="Update your name and email address" />

                <Form
                    {...ProfileController.update.form()}
                    options={{ preserveScroll: true }}
                    disableWhileProcessing
                    className="space-y-6 inert:pointer-events-none inert:grayscale-100"
                >
                    {({ processing, errors }) => (
                        <>
                            <Field data-invalid={!!errors['name']}>
                                <Label htmlFor="name">Name</Label>

                                <Input
                                    id="name"
                                    className="mt-1 block w-full"
                                    defaultValue={auth.user.name}
                                    name="name"
                                    required
                                    autoComplete="name"
                                    placeholder="Full name"
                                    aria-invalid={!!errors['name']}
                                />

                                <InputError className="mt-2" message={errors['name']} />
                            </Field>

                            <Field data-invalid={!!errors['email']}>
                                <Label htmlFor="email">Email address</Label>

                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    defaultValue={auth.user.email}
                                    name="email"
                                    required
                                    autoComplete="username"
                                    placeholder="Email address"
                                    aria-invalid={!!errors['email']}
                                />

                                <InputError className="mt-2" message={errors['email']} />
                            </Field>

                            <Activity
                                mode={mustVerifyEmail && auth.user.email_verified_at === null ? 'visible' : 'hidden'}
                            >
                                <div>
                                    <p className="-mt-4 text-sm text-muted-foreground">
                                        Your email address is unverified.{' '}
                                        <Link
                                            href={send()}
                                            as="button"
                                            className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                        >
                                            Click here to re-send the verification email.
                                        </Link>
                                    </p>

                                    <Activity mode={status === 'verification-link-sent' ? 'visible' : 'hidden'}>
                                        <div className="mt-2 text-sm font-medium text-green-600">
                                            A new verification link has been sent to your email address.
                                        </div>
                                    </Activity>
                                </div>
                            </Activity>

                            <div className="flex items-center gap-4">
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner aria-hidden />
                                </Activity>
                                <Button type="submit" isDisabled={processing} data-test="update-profile-button">
                                    Save
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>

            <DeleteUser />
        </>
    );
}

Profile.layout = {
    breadcrumbs: [
        {
            title: 'Profile settings',
            href: edit(),
        },
    ],
};
