import RoleBadge from '@/components/common/role-badge';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { useInitials } from '@/hooks/use-initials';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import profile from '@/routes/profile';
import { BreadcrumbItem, SharedData } from '@/types';
import { UserWithRelations } from '@/types/application/user';
import { Head, Link, usePage } from '@inertiajs/react';
import { format } from 'date-fns';
import { AlertTriangle, ArrowLeft, EditIcon, Trash2 } from 'lucide-react';
import { Activity } from 'react';

interface UserShowProps {
    user: UserWithRelations & Required<Pick<UserWithRelations, 'permissions' | 'addresses'>>;
}

export default function UserShow({ user }: Readonly<UserShowProps>) {
    const getInitials = useInitials();
    const { auth } = usePage<SharedData>().props;
    const { can } = usePermission();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Users', href: administrative.users.index().url },
        { title: `#${user.name}`, href: administrative.users.show(user.id).url },
    ];

    const isCurrentUser = auth.user?.id === user.id;
    const isUserSuperAdmin = user?.roles.some((role) => role.name === 'super-admin');

    const canEdit = can('user.edit') && !isCurrentUser;
    const canSoftDelete = can('user.destroy') && !isCurrentUser && !user.deleted_at;
    const canPermanentDelete = can('user.destroy') && !isCurrentUser;

    const editLink = canEdit ? administrative.users.edit(user.id) : profile.edit();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Show User: ${user.name}`} />

            <PageLayout
                title="User Details"
                description="You are viewing the user details of this user. Be careful with their personal information."
            >
                <div className="grid gap-8">
                    {/* Header with Back Button */}
                    <header className="grid gap-4">
                        <Link href={administrative.users.index()} prefetch>
                            <Button variant="outline" size="sm">
                                <ArrowLeft aria-hidden size={16} />
                                List Users
                            </Button>
                        </Link>

                        <div className="flex flex-wrap items-center-safe gap-2">
                            <Avatar className="size-28 overflow-hidden rounded-full">
                                <AvatarImage
                                    src={user.avatar.src ?? ''}
                                    srcSet={user.avatar.srcSet ?? ''}
                                    alt={user.name}
                                    className="object-cover"
                                />
                                <AvatarFallback className="rounded-lg bg-neutral-200 text-4xl text-black dark:bg-neutral-700 dark:text-white">
                                    {getInitials(user.name)}
                                </AvatarFallback>
                            </Avatar>

                            <div className="flex flex-col gap-4">
                                <div className="flex flex-wrap items-center gap-2">
                                    <h1 className="text-text-balance text-3xl font-bold">{user.name}</h1>

                                    {user.roles.length > 0 && (
                                        <ul className="flex flex-wrap items-center gap-1">
                                            {user.roles.map((role) => (
                                                <RoleBadge key={role.name} role={role} />
                                            ))}
                                        </ul>
                                    )}
                                </div>

                                <p className="text-muted-foreground">{user.email}</p>
                            </div>
                        </div>
                    </header>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap gap-3">
                        <Activity mode={can('user.edit') ? 'visible' : 'hidden'}>
                            <Link href={editLink} prefetch>
                                <Button variant="secondary">
                                    <EditIcon aria-hidden size={16} />
                                    Edit User
                                </Button>
                            </Link>
                        </Activity>

                        <Activity mode={canSoftDelete ? 'visible' : 'hidden'}>
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive">
                                        <Trash2 aria-hidden size={16} />
                                        Soft Delete
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Soft Delete User?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This will soft delete the user. The user data will be hidden but can be
                                            restored later.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={administrative.users.destroy(user.id)} method="delete">
                                                Soft Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>

                        <Activity mode={canPermanentDelete ? 'visible' : 'hidden'}>
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button
                                        variant="outline"
                                        className="border-destructive bg-transparent text-destructive hover:bg-destructive hover:text-destructive-foreground"
                                    >
                                        <AlertTriangle aria-hidden size={16} />
                                        Permanent Delete
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Permanently Delete User?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. The user and all associated data will be
                                            permanently deleted from the system.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={administrative.users.forceDestroy(user.id)} method="delete">
                                                Permanently Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>
                    </div>

                    {/* User Information */}
                    <div className="grid gap-6">
                        {/* Basic Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Account Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <dl className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Name</dt>
                                        <dd className="mt-1 text-base">{user.name}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Email</dt>
                                        <dd className="mt-1 text-base">{user.email}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Email Verified</dt>
                                        <dd className="mt-1 text-base">
                                            {user.email_verified_at ? (
                                                <Badge
                                                    variant="outline"
                                                    className="bg-green-50 text-green-700 select-none"
                                                >
                                                    Verified
                                                </Badge>
                                            ) : (
                                                <Badge
                                                    variant="outline"
                                                    className="bg-yellow-50 text-yellow-700 select-none"
                                                >
                                                    Not Verified
                                                </Badge>
                                            )}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Two-Factor Authentication
                                        </dt>
                                        <dd className="mt-1 text-base">
                                            {user.two_factor_confirmed_at ? (
                                                <Badge
                                                    variant="outline"
                                                    className="bg-green-50 text-green-700 select-none"
                                                >
                                                    Enabled
                                                </Badge>
                                            ) : (
                                                <Badge
                                                    variant="outline"
                                                    className="bg-gray-50 text-gray-700 select-none"
                                                >
                                                    Disabled
                                                </Badge>
                                            )}
                                        </dd>
                                    </div>
                                </dl>
                            </CardContent>
                        </Card>

                        {/* Permissions */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Permissions</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-wrap gap-2">
                                    {user.permissions.length > 0 ? (
                                        user.permissions.map((permission) => (
                                            <Badge
                                                key={permission.name}
                                                className="bg-blue-100 text-blue-800 select-none"
                                            >
                                                {permission.title}
                                            </Badge>
                                        ))
                                    ) : (
                                        <p className="text-sm text-muted-foreground">
                                            {isUserSuperAdmin
                                                ? 'This user is a Super Admin and has all permissions.'
                                                : 'No permissions assigned to this user.'}
                                        </p>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Address Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Address Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {user.addresses.length > 0 ? (
                                    user.addresses.map((address, index) => (
                                        <div key={address.id}>
                                            <Activity mode={index > 0 ? 'visible' : 'hidden'}>
                                                <Separator className="mb-6" />
                                            </Activity>
                                            <div className="space-y-3">
                                                <div className="flex items-center justify-between">
                                                    <h3 className="font-semibold">{address.label}</h3>
                                                    <Activity mode={address.is_primary ? 'visible' : 'hidden'}>
                                                        <Badge variant="secondary">Primary</Badge>
                                                    </Activity>
                                                </div>
                                                <dl className="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                                                    <div>
                                                        <dt className="text-muted-foreground">Street</dt>
                                                        <dd>
                                                            {address.street}, {address.number}
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt className="text-muted-foreground">Complement</dt>
                                                        <dd>{address.complement}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="text-muted-foreground">Neighborhood</dt>
                                                        <dd>{address.neighborhood}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="text-muted-foreground">City</dt>
                                                        <dd>
                                                            {address.city}, {address.state}
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt className="text-muted-foreground">Postal Code</dt>
                                                        <dd>{address.postal_code}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="text-muted-foreground">Country</dt>
                                                        <dd>{address.country}</dd>
                                                    </div>
                                                </dl>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <p className="text-sm text-muted-foreground">No addresses registered</p>
                                )}
                            </CardContent>
                        </Card>

                        {/* Metadata */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Metadata</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3 text-sm">
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p className="text-muted-foreground">Account Created</p>
                                        <p>{format(user.created_at, 'PPPp')}</p>
                                    </div>
                                    <div>
                                        <p className="text-muted-foreground">Last Updated</p>
                                        <p>{format(user.updated_at, 'PPPp')}</p>
                                    </div>
                                    <div>
                                        <p className="text-muted-foreground">Status</p>
                                        <p>{user.deleted_at ? 'Deleted' : 'Active'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
