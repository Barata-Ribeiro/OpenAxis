import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { LucideIcon } from 'lucide-react';

type Props = {
    items: {
        title: string;
        value: string | null;
        Icon?: LucideIcon;
    }[];
};

export function Metrics({ items }: Readonly<Props>) {
    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            {items
                .filter((item) => item.value !== null)
                .map(({ title, value, Icon }) => (
                    <Card key={title} className="w-full max-w-xs">
                        <CardHeader className="flex items-center justify-between">
                            <CardTitle className="text-sm font-medium text-muted-foreground">{title}</CardTitle>
                            {Icon && (
                                <div className="flex size-8 shrink-0 items-center justify-center rounded-sm bg-primary/10 text-primary [&>svg]:size-4.5">
                                    <Icon aria-hidden />
                                </div>
                            )}
                        </CardHeader>
                        <CardContent>
                            <p className="text-3xl font-semibold tracking-tight">{value}</p>
                        </CardContent>
                    </Card>
                ))}
        </div>
    );
}
