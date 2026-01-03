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
import { Button } from '@/components/ui/button';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { PayableWithRelations } from '@/types/erp/payable';
import { Head, Link } from '@inertiajs/react';
import { AlertTriangle, ArrowLeft, EditIcon } from 'lucide-react';
import { Activity } from 'react';

interface PayableShowPageProps {
    payable: PayableWithRelations;
}

export default function PayableShowPage({ payable }: Readonly<PayableShowPageProps>) {
    const { can } = usePermission();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Payables', href: erp.payables.index().url },
        { title: `#${payable.code}`, href: erp.payables.show(payable.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Payable #${payable.code}`} />

            <PageLayout
                title={`Payable #${payable.code}`}
                description="You are viewing the details of this payable record. Handle with care."
            >
                <div className="grid gap-8">
                    {/* Header with Back Button */}
                    <header className="grid gap-4">
                        <Link href={erp.payables.index()} prefetch>
                            <Button variant="outline" size="sm">
                                <ArrowLeft aria-hidden size={16} />
                                List Payables
                            </Button>
                        </Link>

                        <div className="flex flex-col gap-2">
                            <h1 className="text-text-balance text-2xl font-bold sm:text-3xl">
                                {payable.code} <span className="text-base">ID: ({payable.id})</span>
                            </h1>
                            <p className="max-w-xl text-balance text-muted-foreground">{payable.description}</p>
                        </div>
                    </header>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap gap-3">
                        <Activity mode={can('finance.edit') ? 'visible' : 'hidden'}>
                            <Link href={erp.payables.edit(payable.id)} prefetch>
                                <Button variant="secondary">
                                    <EditIcon aria-hidden size={16} />
                                    Edit Payable
                                </Button>
                            </Link>
                        </Activity>

                        <Activity mode={can('finance.destroy') ? 'visible' : 'hidden'}>
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
                                        <AlertDialogTitle>Permanently Delete Payable?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. The payable and all associated data will be
                                            permanently deleted from the system.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={erp.payables.destroy(payable.id)} method="delete">
                                                Permanently Delete
                                            </Link>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </Activity>
                    </div>

                    {/* TODO: Show payable details below */}
                </div>
            </PageLayout>
        </AppLayout>
    );
}
