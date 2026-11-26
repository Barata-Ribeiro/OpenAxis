import type { FormErrors, FormValues } from '@/types/erp/forms';

export const productImageTypes = ['image/jpeg', 'image/png', 'image/webp'];

export function scaledInput(value: number, precision: number) {
    return (value / 10 ** precision).toFixed(precision);
}

export function scaleFields(data: FormValues, fields: Record<string, number>) {
    const result = { ...data };
    for (const name of ['storage_location_id', 'parent_id']) {
        if (result[name] === '__none__') result[name] = null;
    }
    for (const [name, precision] of Object.entries(fields)) {
        const value = data[name];
        if (typeof value === 'string' && value !== '') {
            result[name] = Math.round(Number(value) * 10 ** precision);
        }
    }
    return result;
}

export function fieldError(errors: FormErrors, name: string) {
    return errors[name] ?? Object.entries(errors).find(([key]) => key.startsWith(name + '.'))?.[1];
}
