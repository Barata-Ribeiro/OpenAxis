import { usePage } from '@inertiajs/react';
import { intlFormat, parseISO } from 'date-fns';

export function useErpLocale() {
    return (usePage().props.auth.locale || 'en').replaceAll('_', '-');
}

export function useErpFormatting() {
    const locale = useErpLocale();

    return {
        money: new Intl.NumberFormat(locale, { style: 'currency', currency: 'BRL' }),
        quantity: new Intl.NumberFormat(locale, { maximumFractionDigits: 3 }),
        date: (value: string, withTime = false) =>
            intlFormat(
                parseISO(value),
                { dateStyle: 'medium', ...(withTime ? { timeStyle: 'short' } : {}) },
                { locale },
            ),
    };
}
