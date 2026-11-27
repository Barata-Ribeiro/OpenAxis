import type { MyColumnMeta } from '@/hooks/data-table/features';

export function humanize(text: string) {
    return text.replaceAll('_', ' ').replace(/^./, (letter) => letter.toUpperCase());
}

export function choices(values: string[]): MyColumnMeta['options'] {
    return values.map((value) => ({ label: humanize(value), value }));
}
