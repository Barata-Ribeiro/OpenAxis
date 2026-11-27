import type { Option } from '@/types/erp/forms';

type ProductValues = {
    name: string;
    description: string;
    storage_location_id: number | null;
    cost_price_cents: number;
    sale_price_cents: number;
    minimum_stock_milliunits: number;
    is_active: boolean;
    category_ids: number[];
};

export type ProductImage = { id: number; url: string | null };
export type ProductFormData = {
    product: ProductValues | null;
    units: Option[];
    locations: Option[];
    categories: Option[];
    images: ProductImage[];
};
