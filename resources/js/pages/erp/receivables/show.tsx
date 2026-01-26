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
import { Separator } from '@/components/ui/separator';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { formatCurrency, normalizeString } from '@/lib/utils';
import { getPaymentMethodIcon } from '@/pages/erp/payables/show';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { receivableStatusLabel, saleOrderStatusLabel } from '@/types/erp/erp-enums';
import type { ReceivableWithRelations } from '@/types/erp/receivable';
import { Head, Link } from '@inertiajs/react';
import { format } from 'date-fns/format';
import {
    AlertTriangle,
    ArrowLeft,
    Calendar,
    CheckSquare,
    Clock,
    DollarSign,
    EditIcon,
    FileText,
    Hash,
    Landmark,
    Mail,
    ShoppingCart,
    StickyNote,
    Truck,
    User,
    Users,
} from 'lucide-react';
import { Activity, createElement } from 'react';

interface ReceivablesShowPageProps {
    receivable: ReceivableWithRelations;
}

export default function ReceivablesShowPage({ receivable }: Readonly<ReceivablesShowPageProps>) {
    const { can } = usePermission();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Receivables', href: erp.receivables.index().url },
        { title: `#${receivable.code}`, href: erp.receivables.show(receivable.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Receivable #${receivable.code}`} />

            <PageLayout
                title={`Receivable #${receivable.code}`}
                description="You are viewing the details of this receivable record. Handle with care."
            >
                <div className="grid gap-8">
                    {/* Header with Back Button */}
                    <header className="grid gap-4">
                        <Link href={erp.receivables.index()} prefetch>
                            <Button variant="outline" size="sm">
                                <ArrowLeft aria-hidden size={16} />
                                List Receivables
                            </Button>
                        </Link>

                        <div className="flex flex-col gap-2">
                            <h1 className="text-text-balance text-2xl font-bold sm:text-3xl">
                                {receivable.code} <span className="text-base">ID: ({receivable.id})</span>
                            </h1>
                            <p className="max-w-xl text-balance text-muted-foreground">{receivable.description}</p>
                        </div>
                    </header>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap gap-3">
                        <Activity mode={can('finance.edit') ? 'visible' : 'hidden'}>
                            <Link href={erp.receivables.edit(receivable.id)} prefetch>
                                <Button variant="secondary">
                                    <EditIcon aria-hidden size={16} />
                                    Edit Receivable
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
                                        <AlertDialogTitle>Permanently Delete Receivable?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. The receivable and all associated data will be
                                            permanently deleted from the system.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                            asChild
                                        >
                                            <Link href={erp.receivables.destroy(receivable.id)} method="delete">
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
                                            {createElement(getPaymentMethodIcon(receivable.payment_method), {
                                                className: 'size-4 text-gray-600',
                                            })}
                                            <span className="capitalize">
                                                {normalizeString(receivable.payment_method)}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Status</dt>
                                        <dd className="mt-1">
                                            <Badge variant="secondary">
                                                {receivableStatusLabel(receivable.status)}
                                            </Badge>
                                        </dd>
                                    </div>
                                </dl>

                                <Separator />

                                <dl className="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Issue Date</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Calendar aria-hidden size={16} className="text-gray-400" />
                                            <time dateTime={receivable.issue_date}>
                                                {format(receivable.issue_date, 'PPP')}
                                            </time>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Due Date</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Calendar aria-hidden size={16} className="text-gray-400" />
                                            <time dateTime={receivable.due_date}>
                                                {format(receivable.due_date, 'PPP')}
                                            </time>
                                        </dd>
                                    </div>
                                </dl>

                                {receivable.received_date && (
                                    <dl>
                                        <dt className="text-sm font-medium text-gray-500">Received Date</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <CheckSquare aria-hidden size={16} className="text-emerald-500" />
                                            <span>{format(receivable.received_date, 'PPP')}</span>
                                        </dd>
                                    </dl>
                                )}

                                {receivable.reference_number && (
                                    <>
                                        <Separator />
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500">Reference Number</dt>
                                            <dd className="mt-1 flex items-center gap-2">
                                                <Hash aria-hidden size={16} className="text-gray-400" />
                                                <span className="font-mono">{receivable.reference_number}</span>
                                            </dd>
                                        </dl>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        {/* Client Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <Users aria-hidden size={20} className="text-blue-600" />
                                    Client Information
                                </CardTitle>
                                <CardDescription>Customer details for this receivable</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500">Client Name</dt>
                                    <dd className="mt-1 text-lg font-semibold">{receivable.client.name}</dd>
                                </dl>

                                <dl>
                                    <dt className="text-sm font-medium text-gray-500">Email</dt>
                                    <dd className="mt-1 flex items-center gap-2">
                                        <Mail aria-hidden size={16} className="text-gray-400" />
                                        <span className="text-sm">{receivable.client.email}</span>
                                    </dd>
                                </dl>

                                <Separator />

                                <dl>
                                    <dt className="text-sm font-medium text-gray-500">Client ID</dt>
                                    <dd className="mt-1 font-mono">#{receivable.client.id}</dd>
                                </dl>
                            </CardContent>
                        </Card>

                        {/* Sales Order Information */}
                        {receivable.sales_order && (
                            <Card className="lg:col-span-2">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-lg">
                                        <ShoppingCart aria-hidden size={20} className="text-violet-600" />
                                        Sales Order Details
                                    </CardTitle>
                                    <CardDescription>Linked sales order information</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <dl className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Order Number</dt>
                                            <dd className="mt-1 font-mono text-lg font-semibold">
                                                {receivable.sales_order.order_number}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Order Status</dt>
                                            <dd className="mt-1">
                                                <Badge variant="outline" className="capitalize">
                                                    {saleOrderStatusLabel(receivable.sales_order.status)}
                                                </Badge>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Order Date</dt>
                                            <dd className="mt-1 flex items-center gap-2">
                                                <Calendar aria-hidden size={16} className="text-gray-400" />
                                                <time dateTime={receivable.sales_order.order_date}>
                                                    {format(receivable.sales_order.order_date, 'PPP')}
                                                </time>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Delivery Date</dt>
                                            <dd className="mt-1 flex items-center gap-2">
                                                <Truck aria-hidden size={16} className="text-gray-400" />
                                                <time dateTime={receivable.sales_order.delivery_date}>
                                                    {format(receivable.sales_order.delivery_date, 'PPpp')}
                                                </time>
                                            </dd>
                                        </div>
                                    </dl>

                                    <Separator className="my-6" />

                                    <dl className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Product Value</dt>
                                            <dd className="mt-1 text-lg font-semibold">
                                                {formatCurrency(receivable.sales_order.product_value)}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Total Cost</dt>
                                            <dd className="mt-1 text-lg font-semibold">
                                                {formatCurrency(receivable.sales_order.total_cost)}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Total Commission</dt>
                                            <dd className="mt-1 text-lg font-semibold text-emerald-600">
                                                {formatCurrency(receivable.sales_order.total_commission)}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Discount</dt>
                                            <dd className="mt-1 text-lg font-semibold">
                                                {formatCurrency(receivable.sales_order.discount_cost)}
                                            </dd>
                                        </div>
                                    </dl>

                                    {receivable.sales_order.notes && (
                                        <>
                                            <Separator className="my-6" />
                                            <div>
                                                <p className="text-sm font-medium text-gray-500">Order Notes</p>
                                                <p className="mt-1 text-gray-700">{receivable.sales_order.notes}</p>
                                            </div>
                                        </>
                                    )}
                                </CardContent>
                            </Card>
                        )}

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
                                {receivable.bank_account ? (
                                    <dl className="space-y-4">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Account Name</dt>
                                            <dd className="mt-1 text-lg font-semibold">
                                                {receivable.bank_account.name}
                                            </dd>
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Bank</dt>
                                                <dd className="mt-1">{receivable.bank_account.bank_name}</dd>
                                            </div>
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Account Number</dt>
                                                <dd className="mt-1 font-mono">
                                                    {receivable.bank_account.account_number}
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
                        {receivable.notes && (
                            <Card className="lg:col-span-2">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-lg">
                                        <StickyNote aria-hidden size={20} className="text-yellow-600" />
                                        Notes
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-balance whitespace-pre-wrap text-gray-700">{receivable.notes}</p>
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
                                                <p className="text-sm font-medium">{receivable.user.name}</p>
                                                <p className="text-xs text-gray-500">{receivable.user.email}</p>
                                            </div>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Created At</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Clock aria-hidden size={16} className="text-gray-400" />
                                            <span>{format(receivable.created_at, 'PPpp')}</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd className="mt-1 flex items-center gap-2">
                                            <Clock aria-hidden size={16} className="text-gray-400" />
                                            <span>{format(receivable.updated_at, 'PPpp')}</span>
                                        </dd>
                                    </div>
                                </dl>

                                {receivable.sales_order_id && (
                                    <>
                                        <Separator className="my-4" />
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500">Linked Sales Order</dt>
                                            <dd className="mt-1 font-mono">Order #{receivable.sales_order_id}</dd>
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
