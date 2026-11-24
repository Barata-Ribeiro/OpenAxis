import { usePage } from '@inertiajs/react';
import { intlFormat, parseISO } from 'date-fns';

export function useErpFormatting() {
    const locale = (usePage().props.auth.locale || 'en').replaceAll('_', '-');

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

export function ErpDate({ value, withTime = false }: Readonly<{ value: string | null; withTime?: boolean }>) {
    const { date } = useErpFormatting();
    return value ? <time dateTime={value.replace(' ', 'T')}>{date(value, withTime)}</time> : '—';
}

export function ErpMoney({ amount }: Readonly<{ amount: number }>) {
    const { money } = useErpFormatting();
    return money.format(amount);
}

export function ErpFee({ rate, type }: Readonly<{ rate: number; type: string }>) {
    const locale = (usePage().props.auth.locale || 'en').replaceAll('_', '-');
    if (type === 'fixed') return <ErpMoney amount={rate / 100} />;

    return new Intl.NumberFormat(locale, { style: 'percent', maximumFractionDigits: 2 }).format(rate / 10_000);
}

export function ErpQuantity({ amount }: Readonly<{ amount: number }>) {
    const { quantity } = useErpFormatting();
    return quantity.format(amount);
}
