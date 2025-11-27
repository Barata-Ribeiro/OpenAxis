import DashboardMonthYearForm from '@/components/forms/dashboard-month-year-form';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { ArrowDown, ArrowUp } from 'lucide-react';

interface StatusInfo {
    title: string;
    value: number;
    delta: number;
    lastMonth: number;
    positive: boolean;
    prefix?: string;
    suffix?: string | null;
}

interface SummaryData {
    title: string;
    [key: string]: StatusInfo | string;
}

interface Data {
    [key: string]: SummaryData;
}

interface AdminDashboardProps {
    data: Data;
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
                <DashboardMonthYearForm />

                <div className="grid gap-6">
                    {Object.entries(data).map(([summaryKey, summary]) => {
                        return (
                            <Card key={summaryKey} className="rounded-md bg-muted/50">
                                <CardHeader>
                                    <CardTitle className="text-3xl">{summary.title}</CardTitle>
                                </CardHeader>

                                <CardContent className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                                    {Object.entries(summary)
                                        .filter(([statusKey]) => statusKey !== 'title')
                                        .map(([statusKey, status]) => {
                                            const info = status as StatusInfo;
                                            const cardKey = `${summaryKey}-${statusKey}`;

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
                                                <Card key={cardKey} className="p-4">
                                                    <CardHeader className="border-0">
                                                        <CardTitle className="text-2xl text-muted-foreground">
                                                            {info.title}
                                                        </CardTitle>
                                                    </CardHeader>

                                                    <CardContent className="space-y-2.5">
                                                        <div className="flex items-center gap-2.5">
                                                            <span className="text-2xl font-medium tracking-tight text-foreground">
                                                                {formattedCurrentValue}
                                                            </span>
                                                            <Badge
                                                                className="select-none"
                                                                variant={changeIndicatorType}
                                                            >
                                                                {valueChangeIndicator}
                                                                {formattedDelta}%
                                                            </Badge>
                                                        </div>
                                                        <div className="mt-2 border-t pt-2.5 text-xs text-muted-foreground">
                                                            Vs last month:{' '}
                                                            <span className="font-medium text-foreground">
                                                                {formattedLastMonth}
                                                            </span>
                                                        </div>
                                                    </CardContent>
                                                </Card>
                                            );
                                        })}
                                </CardContent>
                            </Card>
                        );
                    })}
                </div>
            </section>
        </AppLayout>
    );
}
