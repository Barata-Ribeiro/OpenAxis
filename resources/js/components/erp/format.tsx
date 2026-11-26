import { useErpFormatting, useErpLocale } from '@/hooks/erp/use-erp-formatting';

export function ErpDate({ value, withTime = false }: Readonly<{ value: string | null; withTime?: boolean }>) {
    const { date } = useErpFormatting();
    return value ? <time dateTime={value.replace(' ', 'T')}>{date(value, withTime)}</time> : '—';
}

export function ErpMoney({ amount }: Readonly<{ amount: number }>) {
    const { money } = useErpFormatting();
    return money.format(amount);
}

export function ErpFee({ rate, type }: Readonly<{ rate: number; type: string }>) {
    const locale = useErpLocale();
    if (type === 'fixed') return <ErpMoney amount={rate / 100} />;

    return new Intl.NumberFormat(locale, { style: 'percent', maximumFractionDigits: 2 }).format(rate / 10_000);
}

export function ErpQuantity({ amount }: Readonly<{ amount: number }>) {
    const { quantity } = useErpFormatting();
    return quantity.format(amount);
}
