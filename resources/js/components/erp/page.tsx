import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { features, MyColumnMeta } from '@/hooks/data-table/features';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import { cn } from '@/lib/utils';
import { usePermission } from '@/hooks/use-permission';
import type { Permission } from '@/types/role-permission';
import { Head, Link } from '@inertiajs/react';
import { ModalLink } from '@inertiaui/modal-react';
import type { ColumnDef } from '@tanstack/react-table';
import type { ReactNode } from 'react';

export function humanize(text: string) {
    return text.replaceAll('_', ' ').replace(/^./, (letter) => letter.toUpperCase());
}

export function choices(values: string[]): MyColumnMeta['options'] {
    return values.map((value) => ({ label: humanize(value), value }));
}

export function tableColumn<T extends { id: number }>(
    key: Extract<keyof T, string>,
    label: string,
    variant: MyColumnMeta['variant'] = 'text',
    render?: (row: T) => ReactNode,
    options?: MyColumnMeta['options'],
): ColumnDef<typeof features, T> {
    return createAppColumnHelper<T>().accessor((row) => row[key], {
        id: key,
        header: label,
        meta: { label, variant, options },
        cell: ({ row, getValue }) => (render ? render(row.original) : String(getValue() ?? '—')),
    }) as ColumnDef<typeof features, T>;
}

export function actionColumn<T extends { id: number }>(render: (row: T) => ReactNode) {
    return createAppColumnHelper<T>().display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        cell: ({ row }) => <div className="flex gap-1">{render(row.original)}</div>,
    });
}

export function ErpPage({
    title,
    actions,
    children,
}: Readonly<{ title: string; actions?: ReactNode; children: ReactNode }>) {
    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title={title} />
            <div className="flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
                {actions && <div className="flex flex-wrap gap-2">{actions}</div>}
            </div>
            {children}
        </section>
    );
}

export function Metrics({ items }: Readonly<{ items: { title: string; value: string | null }[] }>) {
    return (
        <dl className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            {items
                .filter((item) => item.value !== null)
                .map(({ title, value }) => (
                    <Card key={title} className="py-4">
                        <CardContent className="space-y-2 px-4">
                            <dt className="text-sm font-medium text-muted-foreground">{title}</dt>
                            <dd className="text-xl font-semibold tabular-nums">{value}</dd>
                        </CardContent>
                    </Card>
                ))}
        </dl>
    );
}

export function ErpLink({
    href,
    children,
    permission,
    modal,
    primary,
    compact,
}: Readonly<{
    href: string;
    children: ReactNode;
    permission?: Permission['name'];
    modal?: boolean;
    primary?: boolean;
    compact?: boolean;
}>) {
    const { can } = usePermission();
    if (permission && !can(permission)) return null;

    const className = cn(
        buttonVariants({
            variant: primary ? 'default' : compact ? 'ghost' : 'outline',
            size: compact ? 'sm' : 'default',
        }),
        'whitespace-nowrap',
    );
    return modal ? (
        <ModalLink href={href} className={className} navigate>
            {children}
        </ModalLink>
    ) : (
        <Link href={href} className={className}>
            {children}
        </Link>
    );
}
