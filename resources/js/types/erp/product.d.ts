import type { ImageSources } from '@/types/index';
import type { Media } from '../application/media';
import type { ProductCategory } from './product-category';

export interface Product {
    id: number;
    sku: string;
    name: string;
    cover_image: ImageSources;
    images?: ImageSources[];
    description?: string;
    slug: string;
    cost_price: string;
    selling_price: string;
    current_stock: number;
    minimum_stock?: number;
    comission: string;
    is_active: boolean;
    product_category_id: number;
    created_at: string;
    updated_at: string;
    deleted_at: string;
}

interface ProductWithRelations extends Product {
    category: Partial<ProductCategory>;
    media?: Media[];
}
