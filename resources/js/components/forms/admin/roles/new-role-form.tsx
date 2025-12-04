import administrative from '@/routes/administrative';
import { Permission } from '@/types/application/role-permission';
import { Deferred, Form, Link } from '@inertiajs/react';

import InputError from '@/components/feedback/input-error';
import InputSkeleton from '@/components/feedback/skeletons/input-skeleton';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { normalizeString } from '@/lib/utils';
import { InfoIcon } from 'lucide-react';
import { Activity, useMemo } from 'react';

interface NewRoleFormProps {
    permissions: Array<Pick<Permission, 'id' | 'title' | 'name'>>;
}

export default function NewRoleForm({ permissions }: Readonly<NewRoleFormProps>) {
    const groupedPermissions = useMemo(() => {
        return (permissions || []).reduce(
            (acc, permission) => {
                const [groupKey] = permission.name.split('.');
                const group = groupKey.toLowerCase();
                if (!acc[group]) acc[group] = [];
                acc[group].push(permission);
                return acc;
            },
            {} as Record<string, Array<Pick<Permission, 'id' | 'title' | 'name'>>>,
        );
    }, [permissions]);

    return (
        <Form
            {...administrative.roles.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field data-invalid={!!errors.name}>
                        <FieldLabel htmlFor="name">Name</FieldLabel>

                        <Input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="e.g. Administrator"
                            required
                            aria-required
                            aria-invalid={!!errors.name}
                        />

                        <InputError message={errors.name} />
                    </Field>

                    <Alert>
                        <InfoIcon aria-hidden />
                        <AlertTitle>Important!</AlertTitle>
                        <AlertDescription>
                            Assigning permissions to roles determines the access level of users associated with those
                            roles. Be cautious when granting permissions to ensure users have appropriate access.
                        </AlertDescription>
                    </Alert>

                    <Field data-invalid={!!errors.permissions}>
                        <FieldLabel htmlFor="permissions">Permissions</FieldLabel>

                        <Deferred data="permissions" fallback={<InputSkeleton />}>
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {Object.entries(groupedPermissions).map(([group, items]) => (
                                    <div key={group} className="space-y-3">
                                        <div className="text-sm font-medium capitalize">{normalizeString(group)}</div>

                                        <div className="flex flex-col gap-2">
                                            {items.map((permission) => (
                                                <div key={permission.id} className="flex items-center gap-2">
                                                    <Checkbox
                                                        id={`permission-${permission.id}`}
                                                        name="permissions[]"
                                                        value={permission.name}
                                                    />
                                                    <Label htmlFor={`permission-${permission.id}`}>
                                                        {permission.title}
                                                    </Label>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </Deferred>

                        <InputError message={errors.permissions} />
                    </Field>

                    <ButtonGroup className="flex-wrap">
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={administrative.roles.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Create Role
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
