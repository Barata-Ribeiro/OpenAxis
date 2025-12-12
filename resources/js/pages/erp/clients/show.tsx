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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { ClientWithRelations } from '@/types/erp/client';
import { clientTypeLabel } from '@/types/erp/erp-enums';
import { Head, Link } from '@inertiajs/react';
import { format } from 'date-fns';
import { AlertTriangle, ArrowLeft, EditIcon, Trash2 } from 'lucide-react';
import { Activity } from 'react';

interface ClientShowProps {
    client: ClientWithRelations;
}

export default function ClientShow({ client }: Readonly<ClientShowProps>) {
    const { can } = usePermission();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Clients', href: erp.clients.index().url },
        { title: `#${client.name}`, href: erp.clients.show(client.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Show Client: ${client.name}`} />

            <PageLayout
                title="Client Details"
                description="You are viewing the client details of this client. Be careful with their personal information."
            >
                <div className="grid gap-8">
                    {/* Header with Back Button */}
                    <header className="grid gap-4">
                        <Link href={erp.clients.index()} prefetch>
                            <Button variant="outline" size="sm">
                                <ArrowLeft aria-hidden size={16} />
                                List Clients
                            </Button>
                        </Link>

                        <div className="flex flex-col gap-2">
                            <h1 className="text-text-balance text-2xl font-bold sm:text-3xl">
                                {client.name} <span className="text-base">ID: ({client.id})</span>
                            </h1>
                            <p className="text-muted-foreground">{client.email}</p>
                        </div>
                    </header>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap gap-3">
                        <Activity mode={can('client.edit') ? 'visible' : 'hidden'}>
                            <Link href={erp.clients.edit(client.id)} prefetch>
                                <Button variant="secondary">
                                    <EditIcon aria-hidden size={16} />
                                    Edit Client
                                </Button>
                            </Link>
                        </Activity>

                        <Activity mode={can('client.destroy') ? 'visible' : 'hidden'}>
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive">
                                        <Trash2 aria-hidden size={16} />
                                        Soft Delete
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Soft Delete Client?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This will soft delete the client. The client data will be hidden but can be
                                            restored later.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={erp.clients.destroy(client.id)} method="delete">
                                                Soft Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>

                        <Activity mode={can('client.destroy') ? 'visible' : 'hidden'}>
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
                                        <AlertDialogTitle>Permanently Delete Client?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. The client and all associated data will be
                                            permanently deleted from the system.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={erp.clients.forceDestroy(client.id)} method="delete">
                                                Permanently Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>
                    </div>

                    {/* Client Information */}
                    <div className="grid gap-6">
                        {/* Basic Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Client Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <dl className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <dt className="text-muted-foreground">Name</dt>
                                        <dd>{client.name}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Email</dt>
                                        <dd>{client.email}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Phone</dt>
                                        <dd>{client.phone_number}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Identification</dt>
                                        <dd>{client.identification}</dd>
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
                                {client.addresses.length > 0 ? (
                                    client.addresses.map((address, index) => (
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
                                <dl className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <dt className="text-muted-foreground">Client Type</dt>
                                        <dd>
                                            <Badge variant="secondary" className="select-none">
                                                {clientTypeLabel(client.client_type)}
                                            </Badge>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt className="text-muted-foreground">Account Created</dt>
                                        <dd>{format(client.created_at, 'PPPp')}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Last Updated</dt>
                                        <dd>{format(client.updated_at, 'PPPp')}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Status</dt>
                                        <dd>{client.deleted_at ? 'Deleted' : 'Active'}</dd>
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
