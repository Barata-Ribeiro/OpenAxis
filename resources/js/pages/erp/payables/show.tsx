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
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Separator } from '@/components/ui/separator';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { payableStatusLabel } from '@/types/erp/erp-enums';
import type { PayableWithRelations } from '@/types/erp/payable';
import { Head, Link } from '@inertiajs/react';
import { differenceInYears, format } from 'date-fns';
import {
    AlertTriangle,
    ArrowLeft,
    BadgeDollarSign,
    Banknote,
    Building2,
    Calendar,
    CheckSquare,
    Clock,
    CreditCard,
    DollarSign,
    EditIcon,
    FileCheck,
    FileText,
    Hash,
    Landmark,
    type LucideIcon,
    Mail,
    Phone,
    StickyNote,
    User,
} from 'lucide-react';
import { Activity, createElement } from 'react';

interface PayableShowPageProps {
    payable: PayableWithRelations;
}

export function getPaymentMethodIcon(method: PayableWithRelations['payment_method']): LucideIcon {
    switch (method) {
        case 'bank_transfer':
            return Building2;
        case 'credit_card':
            return CreditCard;
        case 'cash':
            return Banknote;
        case 'check':
            return FileCheck;
        default:
            return CreditCard;
    }
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

                    <div className="grid gap-6 lg:grid-cols-2">
                        {/* Payment Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <DollarSign aria-hidden size={20} className="text-emerald-600" />
                                    Payment Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <dl className="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Payment Method</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            {createElement(getPaymentMethodIcon(payable.payment_method), {
                                                className: 'size-4 text-gray-600',
                                            })}
                                            <span className="capitalize">
                                                {normalizeString(payable.payment_method)}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Status</dt>
                                        <dd className="mt-1">
                                            <Badge variant="secondary">{payableStatusLabel(payable.status)}</Badge>
                                        </dd>
                                    </div>
                                </dl>

                                <Separator />

                                <dl className="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Issue Date</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Calendar aria-hidden size={16} className="text-gray-400" />
                                            <span>{format(payable.issue_date, 'PPpp')}</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Due Date</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Calendar aria-hidden size={16} className="text-gray-400" />
                                            <span>{format(payable.due_date, 'PPpp')}</span>
                                        </dd>
                                    </div>
                                </dl>

                                {payable.payment_date && (
                                    <dl>
                                        <dt className="text-sm font-medium text-gray-500">Payment Date</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <CheckSquare aria-hidden size={16} className="text-emerald-500" />
                                            <span>{format(payable.payment_date, 'PPpp')}</span>
                                        </dd>
                                    </dl>
                                )}

                                {payable.reference_number && (
                                    <>
                                        <Separator />
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500">Reference Number</dt>
                                            <dd className="mt-1 flex items-center gap-2">
                                                <Hash aria-hidden size={16} className="text-gray-400" />
                                                <span className="font-mono">{payable.reference_number}</span>
                                            </dd>
                                        </dl>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        {/* Supplier Information */}
                        {payable.supplier_id ? (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-lg">
                                        <Building2 aria-hidden size={20} className="text-blue-600" />
                                        Supplier Information
                                    </CardTitle>
                                    <CardDescription>Partner details for this payable</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <dl>
                                        <dt className="text-sm font-medium text-gray-500">
                                            Supplier Name (Company/Individual)
                                        </dt>
                                        <dd className="mt-1 text-lg font-semibold">{payable.supplier.name}</dd>
                                    </dl>

                                    <dl className="grid grid-cols-2 gap-4">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Email</dt>
                                            <div className="mt-1 flex items-center gap-2">
                                                <Mail aria-hidden size={16} className="text-gray-400" />
                                                <dd className="text-sm">{payable.supplier.email}</dd>
                                            </div>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Phone</dt>
                                            <div className="mt-1 flex items-center gap-2">
                                                <Phone aria-hidden size={16} className="text-gray-400" />
                                                <dd className="text-sm">{payable.supplier.phone_number}</dd>
                                            </div>
                                        </div>
                                    </dl>

                                    <Separator />

                                    <dl>
                                        <dt className="text-sm font-medium text-gray-500">
                                            Identification (CNPJ/CPF/SSN/etc.)
                                        </dt>
                                        <dd className="mt-1 font-mono">{payable.supplier.identification}</dd>
                                    </dl>

                                    <dl>
                                        <dt className="text-sm font-medium text-gray-500">Status</dt>
                                        <Badge
                                            variant={payable.supplier.is_active ? 'default' : 'secondary'}
                                            className="mt-1"
                                        >
                                            {payable.supplier.is_active ? 'Active' : 'Inactive'}
                                        </Badge>
                                    </dl>
                                </CardContent>
                            </Card>
                        ) : (
                            <Empty className="border border-dashed">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <AlertTriangle aria-hidden size={48} />
                                    </EmptyMedia>
                                    <EmptyTitle>No Supplier Assigned</EmptyTitle>
                                    <EmptyDescription>
                                        This payable does not have a supplier associated with it. It probably is the
                                        vendor&apos;s commission.
                                    </EmptyDescription>
                                </EmptyHeader>
                            </Empty>
                        )}

                        {/* Vendor Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <User className="text-violet-600" />
                                    Vendor Information
                                </CardTitle>
                                <CardDescription>Sales representative details</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500">Full Name</dt>
                                    <dd className="mt-1 text-lg font-semibold">{payable.vendor.full_name}</dd>
                                </dl>

                                <dl className="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Phone aria-hidden size={16} className="text-gray-400" />
                                            <span className="text-sm">{payable.vendor.phone_number}</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Commission Rate</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <BadgeDollarSign aria-hidden size={16} className="text-gray-400" />
                                            <span className="text-sm">{payable.vendor.commission_rate}%</span>
                                        </dd>
                                    </div>
                                </dl>

                                <Separator />

                                <dl className="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Date of Birth</dt>
                                        <dd className="mt-1">
                                            {format(payable.vendor.date_of_birth, 'PPpp')} (
                                            {differenceInYears(new Date(), payable.vendor.date_of_birth)} years old)
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Status</dt>
                                        <Badge
                                            variant={payable.vendor.is_active ? 'default' : 'secondary'}
                                            className="mt-1"
                                        >
                                            {payable.vendor.is_active ? 'Active' : 'Inactive'}
                                        </Badge>
                                    </div>
                                </dl>
                            </CardContent>
                        </Card>

                        {/* Bank Account Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <Landmark aria-hidden size={20} className="text-amber-600" />
                                    Bank Account
                                </CardTitle>
                                <CardDescription>Payment destination details</CardDescription>
                            </CardHeader>
                            <CardContent>
                                {payable.bank_account ? (
                                    <dl className="space-y-4">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Account Name</dt>
                                            <dd className="mt-1 text-lg font-semibold">{payable.bank_account.name}</dd>
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Bank</dt>
                                                <dd className="mt-1">{payable.bank_account.bank_name}</dd>
                                            </div>
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Account Number</dt>
                                                <dd className="mt-1 font-mono">
                                                    {payable.bank_account.account_number}
                                                </dd>
                                            </div>
                                        </div>
                                    </dl>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-8 text-center">
                                        <Landmark aria-hidden size={48} className="text-gray-300" />
                                        <p className="mt-2 text-sm text-gray-500">No bank account assigned</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Notes Section */}
                        {payable.notes && (
                            <Card className="lg:col-span-2">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-lg">
                                        <StickyNote aria-hidden size={20} className="text-yellow-600" />
                                        Notes
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-balance whitespace-pre-wrap text-gray-700">{payable.notes}</p>
                                </CardContent>
                            </Card>
                        )}

                        {/* Metadata */}
                        <Card className="lg:col-span-2">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <FileText aria-hidden size={20} className="text-gray-600" />
                                    Record Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <dl className="grid gap-4 sm:grid-cols-3">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Created By</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <div className="flex size-8 items-center justify-center rounded-full bg-gray-200">
                                                <User aria-hidden size={16} className="text-gray-600" />
                                            </div>
                                            <div>
                                                <p className="text-sm font-medium">{payable.user.name}</p>
                                                <p className="text-xs text-gray-500">{payable.user.email}</p>
                                            </div>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Created At</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Clock aria-hidden size={16} className="text-gray-400" />
                                            <span>{format(payable.created_at, 'PPpp')}</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Clock aria-hidden size={16} className="text-gray-400" />
                                            <span>{format(payable.updated_at, 'PPpp')}</span>
                                        </dd>
                                    </div>
                                </dl>

                                {payable.sales_order_id && (
                                    <>
                                        <Separator className="my-4" />
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500">Linked Sales Order</dt>
                                            <dd className="mt-1 font-mono">Order #{payable.sales_order_id}</dd>
                                        </dl>
                                    </>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
