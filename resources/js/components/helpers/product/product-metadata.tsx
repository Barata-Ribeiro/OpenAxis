import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { ProductWithRelations } from '@/types/erp/product';
import { format } from 'date-fns';
import { Calendar, Clock, LinkIcon } from 'lucide-react';

interface ProductMetadataProps {
    product: Omit<ProductWithRelations, 'description' | 'images' | 'cover_image'>;
}

export default function ProductMetadata({ product }: Readonly<ProductMetadataProps>) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base">Metadata</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="flex items-center gap-3">
                    <div className="flex size-8 items-center justify-center rounded-md bg-muted">
                        <LinkIcon aria-hidden size={16} className="text-muted-foreground" />
                    </div>
                    <div className="space-y-0.5">
                        <p className="text-xs text-muted-foreground">Slug</p>
                        <p className="font-mono text-sm">{product.slug}</p>
                    </div>
                </div>

                <div className="flex items-center gap-3">
                    <div className="flex size-8 items-center justify-center rounded-md bg-muted">
                        <Calendar aria-hidden size={16} className="text-muted-foreground" />
                    </div>
                    <div className="space-y-0.5">
                        <p className="text-xs text-muted-foreground">Created</p>
                        <p className="text-sm">{format(product.created_at, 'PPPp')}</p>
                    </div>
                </div>

                <div className="flex items-center gap-3">
                    <div className="flex size-8 items-center justify-center rounded-md bg-muted">
                        <Clock aria-hidden size={16} className="text-muted-foreground" />
                    </div>
                    <div className="space-y-0.5">
                        <p className="text-xs text-muted-foreground">Last Updated</p>
                        <p className="text-sm">{format(product.updated_at, 'PPPp')}</p>
                    </div>
                </div>

                {product.deleted_at && (
                    <div className="rounded-md bg-destructive/10 p-3">
                        <p className="text-xs text-destructive">Deleted at</p>
                        <p className="text-sm text-destructive">{format(product.deleted_at, 'PPPp')}</p>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
