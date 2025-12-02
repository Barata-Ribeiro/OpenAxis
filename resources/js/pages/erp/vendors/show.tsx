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
import erp from '@/routes/erp';
import { BreadcrumbItem } from '@/types';
import { roleLabel } from '@/types/application/enums';
import { UserWithRelations } from '@/types/application/user';
import { VendorWithRelations } from '@/types/erp/vendor';
import { Head, Link } from '@inertiajs/react';
import { differenceInCalendarYears, format } from 'date-fns';
import { AlertTriangle, ArrowLeft, EditIcon, Trash2 } from 'lucide-react';
import { Activity } from 'react';

interface VendorShowProps {
    vendor: VendorWithRelations;
}

export default function VendorShow({ vendor }: Readonly<VendorShowProps>) {
    const getInitials = useInitials();
    const { can } = usePermission();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Vendors', href: erp.vendors.index().url },
        { title: `#${vendor.full_name}`, href: erp.vendors.show(vendor.id).url },
    ];

    const user = vendor.user as UserWithRelations;

    const isActive = vendor.is_active;
    const statusLabel = isActive ? 'Active' : 'Inactive';
    const statusVariant = isActive ? 'secondary' : 'destructive';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Show Vendor: ${vendor.full_name}`} />

            <PageLayout
                title="Vendor Details"
                description="You are viewing the vendor details of this vendor. Be careful with their personal information."
            >
                <div className="grid gap-8">
                    {/* Header with Back Button */}
                    <header className="grid gap-4">
                        <Link href={erp.vendors.index()} prefetch>
                            <Button variant="outline" size="sm">
                                <ArrowLeft aria-hidden size={16} />
                                List Vendors
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

                            <div className="flex flex-col gap-2">
                                <h1 className="text-text-balance text-2xl font-bold sm:text-3xl">
                                    {vendor.full_name} <span className="text-base">ID: ({vendor.id})</span>
                                </h1>
                                <p className="text-muted-foreground">{user.email}</p>
                                <Badge
                                    variant={statusVariant}
                                    className="select-none"
                                    aria-label="Vendor profile status"
                                    title="Vendor profile status"
                                >
                                    {statusLabel}
                                </Badge>
                            </div>
                        </div>
                    </header>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap gap-3">
                        <Activity mode={can('vendor.edit') ? 'visible' : 'hidden'}>
                            <Link href={erp.vendors.edit(vendor.id)} prefetch>
                                <Button variant="secondary">
                                    <EditIcon aria-hidden size={16} />
                                    Edit Vendor
                                </Button>
                            </Link>
                        </Activity>

                        <Activity mode={can('vendor.destroy') ? 'visible' : 'hidden'}>
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive">
                                        <Trash2 aria-hidden size={16} />
                                        Soft Delete
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Soft Delete Vendor?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This will soft delete the vendor. The vendor data will be hidden but can be
                                            restored later.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={erp.vendors.destroy(vendor.id)} method="delete">
                                                Soft Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>

                        <Activity mode={can('vendor.destroy') ? 'visible' : 'hidden'}>
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
                                        <AlertDialogTitle>Permanently Delete Vendor?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. The vendor and all associated data will be
                                            permanently deleted from the system.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={erp.vendors.forceDestroy(vendor.id)} method="delete">
                                                Permanently Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>
                    </div>

                    {/* Vendor Information */}
                    <div className="grid gap-6">
                        {/* Basic Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Basic Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <dl className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Full Name</dt>
                                        <dd className="mt-1 text-base">{vendor.full_name}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Birth Date</dt>
                                        <dd className="mt-1 inline-flex items-center gap-x-2 text-base">
                                            {format(vendor.date_of_birth, 'PPP')}
                                            <span className="text-sm text-muted-foreground">
                                                ({differenceInCalendarYears(new Date(), vendor.date_of_birth)} years
                                                old)
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Phone</dt>
                                        <dd className="mt-1 text-base">
                                            <a
                                                href={`tel:${vendor.phone_number}`}
                                                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                {vendor.phone_number}
                                            </a>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-muted-foreground">Comission Rate</dt>
                                        <dd className="mt-1 text-base">{vendor.commission_rate}%</dd>
                                    </div>
                                </dl>
                            </CardContent>
                        </Card>

                        {/* Account Information */}
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

                        {/* Address Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Address Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {user.addresses && user.addresses.length > 0 ? (
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
                                    <p className="text-sm text-muted-foreground">
                                        No addresses available for this vendor.
                                    </p>
                                )}
                            </CardContent>
                        </Card>

                        {/* Metadata */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Metadata</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3 text-sm">
                                <dl className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <dt className="text-muted-foreground">Roles</dt>
                                        <dd>
                                            {user.roles.map((role) => (
                                                <Badge key={role.id} variant="secondary" className="select-none">
                                                    {roleLabel(role.name)}
                                                </Badge>
                                            ))}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt className="text-muted-foreground">Account Created</dt>
                                        <dd>{format(vendor.created_at, 'PPPp')}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Last Updated</dt>
                                        <dd>{format(vendor.updated_at, 'PPPp')}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Account Status</dt>
                                        <dd>{vendor.deleted_at ? 'Deleted' : 'Active'}</dd>
                                    </div>
                                </dl>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
