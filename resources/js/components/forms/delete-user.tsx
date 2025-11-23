import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import HeadingSmall from '@/components/common/heading-small';
import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { Form } from '@inertiajs/react';
import { Activity, useRef } from 'react';

export default function DeleteUser() {
    const passwordInput = useRef<HTMLInputElement>(null);

    return (
        <div className="space-y-6">
            <HeadingSmall title="Delete account" description="Delete your account and all of its resources" />
            <div className="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                <div className="relative space-y-0.5 text-red-600 dark:text-red-100">
                    <p className="font-medium">Warning</p>
                    <p className="text-sm">Please proceed with caution, this cannot be undone.</p>
                </div>

                <Dialog>
                    <DialogTrigger asChild>
                        <Button variant="destructive" data-test="delete-user-button">
                            Delete account
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
                        <DialogDescription>
                            Once your account is deleted, all of its resources and data will also be permanently
                            deleted. Please enter your password to confirm you would like to permanently delete your
                            account.
                        </DialogDescription>

                        <Form
                            {...ProfileController.destroy.form()}
                            options={{ preserveScroll: true }}
                            onError={() => passwordInput.current?.focus()}
                            resetOnSuccess
                            disableWhileProcessing
                            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
                        >
                            {({ resetAndClearErrors, processing, errors }) => (
                                <>
                                    <Field data-invalid={!!errors.password}>
                                        <FieldLabel htmlFor="password" className="sr-only">
                                            Password
                                        </FieldLabel>

                                        <Input
                                            type="password"
                                            id="password"
                                            name="password"
                                            ref={passwordInput}
                                            placeholder="Password"
                                            autoComplete="current-password"
                                            required
                                            aria-required
                                            aria-invalid={!!errors.password}
                                        />

                                        <InputError message={errors.password} />
                                    </Field>

                                    <DialogFooter className="gap-2">
                                        <DialogClose asChild>
                                            <Button variant="secondary" onClick={() => resetAndClearErrors()}>
                                                Cancel
                                            </Button>
                                        </DialogClose>

                                        <Button
                                            type="submit"
                                            variant="destructive"
                                            disabled={processing}
                                            data-test="confirm-delete-user-button"
                                        >
                                            <Activity mode={processing ? 'visible' : 'hidden'}>
                                                <Spinner />
                                            </Activity>
                                            Delete account
                                        </Button>
                                    </DialogFooter>
                                </>
                            )}
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    );
}
