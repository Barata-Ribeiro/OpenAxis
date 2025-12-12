import { AnimatedCard } from '@/components/animated-card';
import ProductDescription from '@/components/helpers/product/product-description';
import { ProductDetails } from '@/components/helpers/product/product-details';
import { ProductImageGallery } from '@/components/helpers/product/product-image-gallery';
import ProductMetadata from '@/components/helpers/product/product-metadata';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { ProductWithRelations } from '@/types/erp/product';
import { Head } from '@inertiajs/react';
import { motion } from 'motion/react';

interface ProductShowProps {
    product: ProductWithRelations;
}

export default function ProductShow({ product }: Readonly<ProductShowProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Products', href: erp.products.index().url },
        { title: `#${product.name}`, href: erp.products.show(product.slug).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Show Product: ${product.name}`} />

            <PageLayout
                title={product.name}
                description={`SKU: ${product.sku} | You are viewing the details for product "${product.name}"`}
            >
                <div className="grid gap-8 lg:grid-cols-2">
                    {/* Left Column - Images */}
                    <motion.div
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.5, ease: 'easeOut' }}
                    >
                        <ProductImageGallery
                            coverImage={product.cover_image}
                            images={product.images}
                            productName={product.name}
                        />
                    </motion.div>

                    {/* Right Column - Details */}
                    <div className="space-y-6">
                        <AnimatedCard delay={0.1}>
                            <ProductDetails product={product} />
                        </AnimatedCard>
                        <AnimatedCard delay={0.2}>
                            <ProductMetadata product={product} />
                        </AnimatedCard>
                    </div>
                </div>

                {/* Description */}
                {product.description && (
                    <AnimatedCard delay={0.3} className="mt-8">
                        <ProductDescription description={product.description} />
                    </AnimatedCard>
                )}
            </PageLayout>
        </AppLayout>
    );
}
