import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';

export default function AdminDashboardSkeleton() {
    return (
        <div className="grid gap-6" role="status" aria-label="Loading dashboard data...">
            {Array.from({ length: 3 }).map((_, sectionIndex) => (
                <Card key={sectionIndex} className="rounded-md bg-muted/50">
                    <CardHeader>
                        <Skeleton className="h-10 w-64" />
                    </CardHeader>

                    <CardContent className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        {Array.from({ length: 4 }).map((__, cardIndex) => (
                            <Card key={cardIndex} className="p-4">
                                <CardHeader className="border-0">
                                    <Skeleton className="h-6 w-40" />
                                </CardHeader>

                                <CardContent className="space-y-2.5">
                                    <div className="flex items-center gap-2.5">
                                        <Skeleton className="h-7 w-28" />
                                        <Skeleton className="h-6 w-20" />
                                    </div>

                                    <div className="mt-2 border-t pt-2.5">
                                        <Skeleton className="h-4 w-44" />
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </CardContent>
                </Card>
            ))}
        </div>
    );
}
