import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { cn, formatCurrency } from '@/lib/utils';
import type { ProductWithRelations } from '@/types/erp/product';
import { AlertTriangleIcon, DollarSignIcon, LayersIcon, PackageIcon, PercentIcon, TagIcon } from 'lucide-react';
import { Activity } from 'react';

interface ProductDetailsProps {
    product: Omit<ProductWithRelations, 'description' | 'images' | 'cover_image'>;
}

export function ProductDetails({ product }: Readonly<ProductDetailsProps>) {
    const profit = Number.parseFloat(product.selling_price) - Number.parseFloat(product.cost_price);
    const profitMargin = (profit / Number.parseFloat(product.selling_price)) * 100;
    const isLowStock = product.minimum_stock !== undefined && product.current_stock < product.minimum_stock;

    return (
        <Card>
            <CardHeader>
                <div className="flex items-start justify-between">
                    <div className="space-y-1">
                        <CardTitle className="text-2xl">{product.name}</CardTitle>
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <TagIcon aria-hidden size={16} />
                            <span>SKU: {product.sku}</span>
                        </div>
                    </div>
                    <Badge className="select-none" variant={product.is_active ? 'secondary' : 'destructive'}>
                        {product.is_active ? 'Active' : 'Inactive'}
                    </Badge>
                </div>
            </CardHeader>

            <CardContent className="space-y-6">
                {/* Category */}
                {product.category?.name && (
                    <div className="flex items-center gap-2">
                        <LayersIcon aria-hidden size={16} className="text-muted-foreground" />
                        <span className="text-sm text-muted-foreground">Category:</span>
                        <Badge variant="outline">{product.category.name}</Badge>
                    </div>
                )}

                <Separator />

                {/* Pricing Section */}
                <div className="space-y-3">
                    <h4 className="flex items-center gap-2 font-medium">
                        <DollarSignIcon aria-hidden size={16} />
                        Pricing
                    </h4>
                    <dl className="grid grid-cols-2 gap-4">
                        <div className="space-y-1">
                            <dt className="text-xs text-muted-foreground">Cost Price</dt>
                            <dd className="text-lg font-semibold">{formatCurrency(product.cost_price)}</dd>
                        </div>
                        <div className="space-y-1">
                            <dt className="text-xs text-muted-foreground">Selling Price</dt>
                            <dd className="text-lg font-semibold text-green-600">
                                {formatCurrency(product.selling_price)}
                            </dd>
                        </div>
                        <div className="space-y-1">
                            <dt className="text-xs text-muted-foreground">Profit</dt>
                            <dd className="text-lg font-semibold">{formatCurrency(profit.toString())}</dd>
                        </div>
                        <div className="space-y-1">
                            <dt className="text-xs text-muted-foreground">Margin</dt>
                            <dd className="text-lg font-semibold">{profitMargin.toFixed(1)}%</dd>
                        </div>
                    </dl>
                </div>

                <Separator />

                {/* Commission */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <PercentIcon aria-hidden size={16} className="text-muted-foreground" />
                        <span className="text-sm">Commission Rate</span>
                    </div>
                    <Badge variant="secondary">{product.comission}%</Badge>
                </div>

                <Separator />

                {/* Stock Section */}
                <div className="space-y-3">
                    <h4 className="flex items-center gap-2 font-medium">
                        <PackageIcon aria-hidden size={16} />
                        Inventory
                    </h4>
                    <dl className="grid grid-cols-2 gap-4">
                        <div className="space-y-1">
                            <dt className="text-xs text-muted-foreground">Current Stock</dt>
                            <dd className="flex items-center gap-2">
                                <p className={cn('text-lg font-semibold', isLowStock ? 'text-destructive' : '')}>
                                    {product.current_stock}
                                </p>
                                <Activity mode={isLowStock ? 'visible' : 'hidden'}>
                                    <AlertTriangleIcon aria-hidden size={16} className="text-destructive" />
                                </Activity>
                            </dd>
                        </div>
                        {product.minimum_stock !== undefined && (
                            <div className="space-y-1">
                                <dt className="text-xs text-muted-foreground">Minimum Stock</dt>
                                <dd className="text-lg font-semibold">{product.minimum_stock}</dd>
                            </div>
                        )}
                    </dl>
                    <Activity mode={isLowStock ? 'visible' : 'hidden'}>
                        <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
                            <div className="flex items-center gap-2">
                                <AlertTriangleIcon aria-hidden size={16} />
                                <span>Stock is below minimum level!</span>
                            </div>
                        </div>
                    </Activity>
                </div>
            </CardContent>
        </Card>
    );
}
