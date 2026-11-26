import type { FileEntry } from '@/components/common/file-upload';
import { productImageTypes, scaleFields } from '@/lib/erp/forms';
import type { FormValues } from '@/types/erp/forms';
import type { ProductImage } from '@/types/erp/products';
import { useState } from 'react';

const ACCEPTED_IMAGES = new Set(productImageTypes);

export function useProductImages(images: ProductImage[]) {
    const [files, setFiles] = useState<FileEntry[]>([]);
    const [removed, setRemoved] = useState<number[]>([]);
    const [feedback, setFeedback] = useState('');
    const available = Math.max(0, 5 - images.length + removed.length);

    function addFiles(selected: File[]) {
        const valid = selected.filter((file) => ACCEPTED_IMAGES.has(file.type) && file.size <= 2 * 1024 * 1024);
        const additions = valid.slice(0, Math.max(0, available - files.length));

        setFiles([
            ...files,
            ...additions.map((file): FileEntry => ({
                id: crypto.randomUUID(),
                file,
                progress: 0,
                state: 'queued',
            })),
        ]);

        const isImageLimitExceeded = additions.length < selected.length;
        const exceedImageLimitMessage = isImageLimitExceeded ? 'A product can have at most five images.' : '';

        setFeedback(
            valid.length < selected.length
                ? 'Choose JPG, PNG or WebP images up to 2 MB each.'
                : exceedImageLimitMessage,
        );
    }

    function toggleRemoval(id: number, selected: boolean) {
        if (!selected && files.length > Math.max(0, available - 1)) {
            setFeedback('Remove a queued image before keeping this image.');
            return;
        }
        setRemoved(selected ? [...removed, id] : removed.filter((item) => item !== id));
        setFeedback('');
    }

    function transform(data: FormValues) {
        return {
            ...scaleFields(data, { cost_price_cents: 2, sale_price_cents: 2, minimum_stock_milliunits: 3 }),
            is_active: data['is_active'] === '1',
            images: files.map(({ file }) => file),
            ...(images.length ? { remove_image_ids: removed } : {}),
        };
    }

    return {
        available,
        files,
        removed,
        feedback,
        addFiles,
        toggleRemoval,
        transform,
        removeFile: (id: string) => {
            setFiles(files.filter((item) => item.id !== id));
            setFeedback('');
        },
    };
}
