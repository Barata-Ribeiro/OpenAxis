import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Sortable, SortableItem, SortableItemHandle } from '@/components/ui/sortable';
import { cn } from '@/lib/utils';
import { CloudUpload, GripVertical, TriangleAlert, XIcon } from 'lucide-react';
import { useCallback, useEffect, useEffectEvent, useState } from 'react';
import { toast } from 'sonner';

export interface ImageFile {
    id: string;
    file: File;
    preview: string;
    progress: number;
    status: 'uploading' | 'completed' | 'error';
    error?: string;
}

interface SortableImage {
    id: string;
    src: string;
    alt: string;
    type: 'default' | 'uploaded';
}

interface ImageUploadProps {
    maxFiles?: number;
    maxSize?: number;
    accept?: string;
    className?: string;
    onImagesChange?: (images: ImageFile[]) => void;
    onOrderChange?: (images: ImageFile[]) => void;
    value?: ImageFile[];
    onRemoveImage?: (id: string) => void;
    resetKey?: string | number;
}

export default function SortableImageUpload({
    maxFiles = 5, // Changed to 5 as per UI reference
    maxSize = 10 * 1024 * 1024, // 10MB as per UI reference
    accept = 'image/*',
    className,
    onImagesChange,
    onOrderChange,
    value,
    onRemoveImage,
    resetKey,
}: Readonly<ImageUploadProps>) {
    const [images, setImages] = useState<ImageFile[]>([]);
    const [isDragging, setIsDragging] = useState(false);
    const [errors, setErrors] = useState<string[]>([]);
    const [allImages, setAllImages] = useState<SortableImage[]>([]);
    const onKeepingImagesInSync = useEffectEvent((value: ImageFile[] | undefined) => {
        if (value) {
            setImages(value);
            setAllImages(value.map(createSortableImage));
        }
    });

    // Helper function to create SortableImage from ImageFile
    const createSortableImage = useCallback(
        (imageFile: ImageFile): SortableImage => ({
            id: imageFile.id,
            src: imageFile.preview,
            alt: imageFile.file.name,
            type: 'uploaded',
        }),
        [],
    );

    // Ensure arrays never contain undefined items
    useEffect(() => onKeepingImagesInSync(value), [value]);

    // Reset internal state when resetKey changes
    useEffect(() => {
        if (resetKey !== undefined) {
            setImages([]);
            setAllImages([]);
            setErrors([]);
        }
    }, [resetKey]);

    const validateFile = useCallback(
        (file: File): string | null => {
            if (!file.type.startsWith('image/')) {
                return 'File must be an image';
            }
            if (file.size > maxSize) {
                return `File size must be less than ${(maxSize / 1024 / 1024).toFixed(1)}MB`;
            }
            if (images.length >= maxFiles) {
                return `Maximum ${maxFiles} files allowed`;
            }
            return null;
        },
        [maxSize, maxFiles, images.length],
    );

    const addImages = useCallback(
        (files: FileList | File[]) => {
            const newImages: ImageFile[] = [];
            const newErrors: string[] = [];

            for (const file of Array.from(files)) {
                const error = validateFile(file);

                if (!error && images.length + newImages.length >= maxFiles) {
                    newErrors.push(`Cannot add ${file.name}: maximum of ${maxFiles} files reached.`);
                    continue;
                }

                if (error) {
                    newErrors.push(`${file.name}: ${error}`);
                    continue;
                }

                const imageFile: ImageFile = {
                    id: `${Date.now()}-${Math.random()}`,
                    file,
                    preview: URL.createObjectURL(file),
                    progress: 0,
                    status: 'uploading',
                };

                newImages.push(imageFile);
            }

            if (newErrors.length > 0) {
                setErrors((prev) => [...prev, ...newErrors]);
            }

            if (newImages.length > 0) {
                const updatedImages = [...images, ...newImages];
                setImages(updatedImages);
                onImagesChange?.(updatedImages);

                // Add new images to allImages for sorting
                const newSortableImages = newImages.map(createSortableImage);
                setAllImages((prev) => [...prev, ...newSortableImages]);
            }
        },
        [validateFile, images, maxFiles, onImagesChange, createSortableImage],
    );

    const removeImage = useCallback(
        (id: string) => {
            // Notify parent first
            onRemoveImage?.(id);

            // Remove from allImages
            setAllImages((prev) => prev.filter((img) => img.id !== id));

            // If it's an uploaded image, also remove from images array and revoke URL
            const uploadedImage = images.find((img) => img.id === id);
            if (uploadedImage) {
                URL.revokeObjectURL(uploadedImage.preview);
                const newImages = images.filter((img) => img.id !== id);
                setImages(newImages);
                onImagesChange?.(newImages);
                onOrderChange?.(newImages);
            }
        },
        [images, onImagesChange, onOrderChange, onRemoveImage],
    );

    const handleDragEnter = useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    }, []);

    const handleDragLeave = useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    }, []);

    const handleDragOver = useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
    }, []);

    const handleDrop = useCallback(
        (e: React.DragEvent) => {
            e.preventDefault();
            e.stopPropagation();
            setIsDragging(false);

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                addImages(files);
            }
        },
        [addImages],
    );

    const openFileDialog = useCallback(() => {
        const input = document.createElement('input');
        input.type = 'file';
        input.multiple = true;
        input.accept = accept;
        input.onchange = (e) => {
            const target = e.target as HTMLInputElement;
            if (target.files) {
                addImages(target.files);
            }
        };
        input.click();
    }, [accept, addImages]);

    const formatBytes = (bytes: number): string => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    };

    return (
        <div className={cn('w-full max-w-4xl', className)}>
            {/* Instructions */}
            <div className="mb-4 text-center">
                <p className="text-sm text-muted-foreground">
                    Upload up to {maxFiles} images (JPG, PNG, GIF, WebP, max {formatBytes(maxSize)} each). <br />
                    Drag and drop images to reorder.
                    {images.length > 0 && ` ${images.length}/${maxFiles} uploaded.`}
                </p>
            </div>

            {/* Image Grid with Sortable */}
            <div className="mb-6">
                {/* Combined Images Sortable */}
                <Sortable
                    value={allImages.map((item) => item.id)}
                    onValueChange={(newItemIds) => {
                        // Reconstruct the allImages array based on the new order
                        const newAllImages = newItemIds
                            .map((itemId) => {
                                // First try to find in allImages (default images)
                                const existingImage = allImages.find((img) => img.id === itemId);
                                if (existingImage) return existingImage;

                                // If not found, it's a newly uploaded image
                                const uploadedImage = images.find((img) => img.id === itemId);
                                if (uploadedImage) {
                                    return createSortableImage(uploadedImage);
                                }
                                return null;
                            })
                            .filter((item): item is SortableImage => item !== null);

                        setAllImages(newAllImages);

                        const newImagesOrder = newAllImages
                            .map((si) => images.find((img) => img.id === si.id))
                            .filter((img): img is ImageFile => !!img);

                        if (newImagesOrder.length === images.length) {
                            setImages(newImagesOrder);
                            onImagesChange?.(newImagesOrder);
                            onOrderChange?.(newImagesOrder);
                        }

                        toast.success('Images reordered successfully!', {
                            description: `Images rearranged across both sections`,
                            duration: 3000,
                        });
                    }}
                    getItemValue={(item) => item}
                    strategy="grid"
                    className="grid auto-rows-fr grid-cols-5 gap-2.5"
                >
                    {allImages.map((item) => (
                        <SortableItem key={item.id} value={item.id}>
                            <div className="group relative flex shrink-0 items-center justify-center rounded-md border border-border bg-accent/50 shadow-none transition-all duration-200 hover:z-10 hover:bg-accent/70 data-[dragging=true]:z-50">
                                <img
                                    src={item.src}
                                    className="pointer-events-none h-[120px] w-full rounded-md object-cover"
                                    alt={item.alt}
                                />

                                {/* Drag Handle */}
                                <SortableItemHandle className="absolute start-2 top-2 cursor-grab opacity-0 group-hover:opacity-100 active:cursor-grabbing">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        className="size-6 rounded-full"
                                        aria-label="Drag to reorder"
                                        title="Drag to reorder"
                                    >
                                        <GripVertical aria-hidden className="size-3.5" />
                                    </Button>
                                </SortableItemHandle>

                                {/* Remove Button Overlay */}
                                <Button
                                    onClick={() => removeImage(item.id)}
                                    variant="outline"
                                    size="icon"
                                    className="absolute end-2 top-2 size-6 rounded-full opacity-0 shadow-sm group-hover:opacity-100"
                                    aria-label="Remove image"
                                    title="Remove image"
                                >
                                    <XIcon aria-hidden className="size-3.5" />
                                </Button>
                            </div>
                        </SortableItem>
                    ))}
                </Sortable>
            </div>

            {/* Upload Area */}
            <Card
                className={cn(
                    'rounded-md border-dashed shadow-none transition-colors',
                    isDragging
                        ? 'border-primary bg-primary/5'
                        : 'border-muted-foreground/25 hover:border-muted-foreground/50',
                )}
                onDragEnter={handleDragEnter}
                onDragLeave={handleDragLeave}
                onDragOver={handleDragOver}
                onDrop={handleDrop}
            >
                <CardContent className="text-center">
                    <div className="mx-auto mb-3 flex size-[32px] items-center justify-center rounded-full border border-border">
                        <CloudUpload className="size-4" />
                    </div>
                    <h3 className="text-2sm mb-0.5 font-semibold text-foreground">
                        Choose a file or drag & drop here.
                    </h3>
                    <span className="mb-3 block text-xs font-normal text-secondary-foreground">
                        JPEG, PNG, up to {formatBytes(maxSize)}.
                    </span>
                    <Button type="button" size="sm" variant="outline" onClick={openFileDialog}>
                        Browse File
                    </Button>
                </CardContent>
            </Card>

            {/* Error Messages */}
            {errors.length > 0 && (
                <Alert variant="destructive" className="mt-5 border-red-500 bg-red-50">
                    <TriangleAlert aria-hidden />
                    <AlertTitle>File(s) related error(s)</AlertTitle>
                    <AlertDescription>
                        <ul className="list-inside list-disc text-sm">
                            {errors.map((error) => (
                                <li key={error} className="last:mb-0">
                                    {error}
                                </li>
                            ))}
                        </ul>
                    </AlertDescription>
                </Alert>
            )}
        </div>
    );
}
