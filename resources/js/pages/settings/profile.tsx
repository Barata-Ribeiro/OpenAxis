import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { send } from '@/routes/verification';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head, Link, usePage } from '@inertiajs/react';

import HeadingSmall from '@/components/common/heading-small';
import InputError from '@/components/feedback/input-error';
import DeleteUser from '@/components/forms/delete-user';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit } from '@/routes/profile';
import { BadgeCheckIcon } from 'lucide-react';
import { Activity } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

export default function Profile({ mustVerifyEmail, status }: Readonly<{ mustVerifyEmail: boolean; status?: string }>) {
    const { auth } = usePage<SharedData>().props;

    const mustVerify = mustVerifyEmail && auth.user.email_verified_at === null;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile information" description="Update your name and email address" />

                    <Form
                        {...ProfileController.update.form()}
                        options={{ preserveScroll: true }}
                        className="space-y-6 transition inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
                        disableWhileProcessing
                    >
                        {({ processing, recentlySuccessful, errors }) => (
                            <>
                                <Field data-invalid={!!errors.name}>
                                    <FieldLabel htmlFor="name">Name</FieldLabel>

                                    <Input
                                        type="text"
                                        id="name"
                                        name="name"
                                        defaultValue={auth.user.name}
                                        autoComplete="name"
                                        placeholder="Full name"
                                        required
                                        aria-required
                                        aria-invalid={!!errors.name}
                                    />

                                    <InputError message={errors.name} />
                                </Field>

                                <Field data-invalid={!!errors.email}>
                                    <FieldLabel htmlFor="email">
                                        <span>Email address</span>
                                        <Activity mode={mustVerify ? 'hidden' : 'visible'}>
                                            <Badge
                                                variant="secondary"
                                                className="bg-blue-500 text-white dark:bg-blue-600"
                                            >
                                                <BadgeCheckIcon aria-hidden />
                                                Verified
                                            </Badge>
                                        </Activity>
                                    </FieldLabel>

                                    <Input
                                        type="email"
                                        id="email"
                                        name="email"
                                        defaultValue={auth.user.email}
                                        autoComplete="username"
                                        placeholder="Email address"
                                        required
                                        aria-required
                                        aria-invalid={!!errors.email}
                                    />

                                    <InputError message={errors.email} />
                                </Field>

                                <Activity mode={mustVerify ? 'visible' : 'hidden'}>
                                    <div>
                                        <p className="-mt-4 text-sm text-muted-foreground">
                                            Your email address is unverified.{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                Click here to resend the verification email.
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
                                    <Button type="submit" disabled={processing} data-test="update-profile-button">
                                        Save
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

                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}
