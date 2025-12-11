import type { ImageSources } from '@/types/index';
import type { InventoryMovementType } from '../application/enums';
import type { Media } from '../application/media';
import type { User } from '../application/user';
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

export interface ProductWithRelations extends Product {
    category: Partial<ProductCategory>;
    media?: Media[];
}

export interface StockMovement {
    id: number;
    product_id: number;
    user_id: number;
    movement_type: InventoryMovementType;
    quantity: number;
    reason: string | null;
    reference: string | null;
    created_at: string;
    updated_at: string;
}

export interface StockMovementWithRelations extends StockMovement {
    product: Partial<Product>;
    user: Partial<User>;
}
