import { cn } from '@/lib/utils';
import type { ImageSources } from '@/types';
import { AnimatePresence, motion } from 'motion/react';
import { Activity, useState } from 'react';

interface ProductImageGalleryProps {
    coverImage: ImageSources;
    images?: ImageSources[];
    productName: string;
}

export function ProductImageGallery({ coverImage, images = [], productName }: Readonly<ProductImageGalleryProps>) {
    const allImages = [coverImage, ...images].filter((img) => img.id);
    const [selectedIndex, setSelectedIndex] = useState(0);
    const selectedImage = allImages[selectedIndex];

    return (
        <div className="space-y-4">
            {/* Main Image with AnimatePresence for smooth transitions */}
            <div className="relative aspect-square overflow-hidden rounded-lg border bg-muted">
                <AnimatePresence mode="wait">
                    {selectedImage?.src && selectedImage?.srcSet ? (
                        <motion.div
                            key={selectedIndex}
                            initial={{ opacity: 0, scale: 1.05 }}
                            animate={{ opacity: 1, scale: 1 }}
                            exit={{ opacity: 0, scale: 0.95 }}
                            transition={{ duration: 0.3, ease: 'easeOut' }}
                            className="absolute inset-0"
                        >
                            <img
                                src={selectedImage.src}
                                srcSet={selectedImage.srcSet}
                                alt={productName}
                                className="aspect-square size-full object-cover"
                                sizes="(max-width: 768px) 100vw, 50vw"
                                width={600}
                                height={600}
                            />
                        </motion.div>
                    ) : (
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            className="flex h-full items-center justify-center text-muted-foreground"
                        >
                            <p className="rounded-lg bg-background p-2">No image available</p>
                        </motion.div>
                    )}
                </AnimatePresence>
            </div>

            {/* Thumbnail Strip with staggered entrance animation */}
            {allImages.length > 1 && (
                <motion.div
                    initial="hidden"
                    animate="visible"
                    variants={{
                        hidden: { opacity: 0 },
                        visible: {
                            opacity: 1,
                            transition: {
                                staggerChildren: 0.08,
                            },
                        },
                    }}
                    className="flex gap-2 p-2"
                >
                    {allImages.map((image, index) => (
                        <motion.button
                            key={image.id}
                            variants={{
                                hidden: { opacity: 0, y: 10 },
                                visible: { opacity: 1, y: 0 },
                            }}
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                            onClick={() => setSelectedIndex(index)}
                            className={cn(
                                'relative size-16 shrink-0 overflow-hidden rounded-md border-2 transition-colors',
                                selectedIndex === index
                                    ? 'border-primary'
                                    : 'border-transparent hover:border-muted-foreground/50',
                            )}
                        >
                            {image.src && image.srcSet && (
                                <img
                                    src={image.src}
                                    srcSet={image.srcSet}
                                    alt={`${productName} - Preview ${index + 1}`}
                                    className="aspect-square size-16 object-cover"
                                    sizes="64px"
                                    width={64}
                                    height={64}
                                />
                            )}

                            <Activity mode={selectedIndex === index ? 'visible' : 'hidden'}>
                                <motion.div
                                    layoutId="thumbnail-indicator"
                                    className="absolute inset-0 rounded-md ring-2 ring-primary ring-offset-2"
                                    transition={{ type: 'spring', stiffness: 300, damping: 30 }}
                                />
                            </Activity>
                        </motion.button>
                    ))}
                </motion.div>
            )}
        </div>
    );
}
