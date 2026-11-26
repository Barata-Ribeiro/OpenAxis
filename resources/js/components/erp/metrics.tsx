import { Card, CardContent } from '@/components/ui/card';

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
