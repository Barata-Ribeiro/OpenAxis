import InputError from '@/components/feedback/input-error';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldLabel } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/react';
import { ArrowDown, ArrowUp } from 'lucide-react';
import { Activity } from 'react';

interface StatusInfo {
    title: string;
    value: number;
    delta: number;
    lastMonth: number;
    positive: boolean;
    prefix?: string;
    suffix?: string | null;
}

interface AdminDashboardProps {
    data: {
        totalSales: StatusInfo;
        totalClients: StatusInfo;
        totalVendors: StatusInfo;
        totalOrders: StatusInfo;
        totalProfits: StatusInfo;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

function formatNumber(n: number): string {
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1);
    if (n >= 1_000) return n.toLocaleString();
    return n.toString();
}

export default function AdminDashboard({ data }: Readonly<AdminDashboardProps>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            <section className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* TODO: Extract form to a component for page readability */}
                <Form
                    {...dashboard.form()}
                    method="GET"
                    className="w-full max-w-64 place-self-end inert:pointer-events-none inert:grayscale-100"
                    disableWhileProcessing
                >
                    {({ processing, errors }) => (
                        <Field data-invalid={!!errors.yearMonth}>
                            <InputGroup>
                                <InputGroupInput id="yearMonth" name="yearMonth" type="month" />
                                <InputGroupAddon align="block-start">
                                    <FieldLabel htmlFor="yearMonth">Select Month & Year</FieldLabel>
                                    <InputGroupButton
                                        type="submit"
                                        className="ml-auto"
                                        size="sm"
                                        variant="default"
                                        disabled={processing}
                                    >
                                        <Activity mode={processing ? 'visible' : 'hidden'}>
                                            <Spinner />
                                        </Activity>
                                        Filter
                                    </InputGroupButton>
                                </InputGroupAddon>
                            </InputGroup>
                            <InputError message={errors.yearMonth} />
                        </Field>
                    )}
                </Form>

                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    {Object.entries(data).map(([key, info]) => {
                        const formattedCurrentValue = `${info.prefix ?? ''}${formatNumber(info.value)}${info.suffix ?? ''}`;
                        const formattedDelta = `${info.positive ? '+' : '-'}${formatNumber(info.delta)}`;
                        const formattedLastMonth = `${info.prefix ?? ''}${formatNumber(info.lastMonth)}${info.suffix ?? ''}`;

                        const valueChangeIndicator = info.positive ? (
                            <ArrowUp aria-hidden />
                        ) : (
                            <ArrowDown aria-hidden />
                        );

                        const changeIndicatorType = info.positive ? 'success' : 'destructive';
                        return (
                            <Card key={key} className="p-4">
                                <CardHeader className="border-0">
                                    <CardTitle className="text-2xl text-muted-foreground">{info.title}</CardTitle>
                                </CardHeader>

                                <CardContent className="space-y-2.5">
                                    <div className="flex items-center gap-2.5">
                                        <span className="text-2xl font-medium tracking-tight text-foreground">
                                            {formattedCurrentValue}
                                        </span>
                                        <Badge className="select-none" variant={changeIndicatorType}>
                                            {valueChangeIndicator}
                                            {formattedDelta}%
                                        </Badge>
                                    </div>
                                    <div className="mt-2 border-t pt-2.5 text-xs text-muted-foreground">
                                        Vs last month:{' '}
                                        <span className="font-medium text-foreground">{formattedLastMonth}</span>
                                    </div>
                                </CardContent>
                            </Card>
                        );
                    })}
                </div>
            </section>
        </AppLayout>
    );
}
