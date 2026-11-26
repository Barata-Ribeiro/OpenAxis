import { ErpDate, ErpFee, ErpMoney } from '@/components/erp/format';
import type { ProductImage } from '@/types/erp/products';

type Detail =
    | string
    | number
    | null
    | { date: string; withTime?: boolean }
    | { cents: number }
    | { feeRate: number; feeType: string };

export type RecordDetailsProps = {
    details: Record<string, Detail>;
    images?: ProductImage[];
};

export function RecordDetails({ details, images }: Readonly<RecordDetailsProps>) {
    return (
        <>
            <dl className="grid gap-3 sm:grid-cols-2">
                {Object.entries(details).map(([label, value]) => (
                    <div className="min-w-0 rounded-md border p-3" key={label}>
                        <dt className="text-sm text-muted-foreground">{label}</dt>
                        <dd className="mt-1 text-sm font-medium wrap-break-word">
                            {value === null || value === '' ? (
                                '—'
                            ) : typeof value !== 'object' ? (
                                value
                            ) : 'date' in value ? (
                                <ErpDate value={value.date} withTime={value.withTime} />
                            ) : 'feeRate' in value ? (
                                <ErpFee rate={value.feeRate} type={value.feeType} />
                            ) : (
                                <ErpMoney amount={value.cents / 100} />
                            )}
                        </dd>
                    </div>
                ))}
            </dl>
            {images?.some((image) => image.url) && (
                <div className="mt-4 flex flex-wrap gap-3">
                    {images.map(
                        ({ url, id }, index) =>
                            url && (
                                <img
                                    key={id}
                                    className="aspect-square w-28 rounded-md border object-cover"
                                    src={url}
                                    alt={'Product image ' + (index + 1)}
                                />
                            ),
                    )}
                </div>
            )}
        </>
    );
}
