export interface ProductCategory {
    id: number;
    name: string;
    description: string;
    slug: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;

    products_count?: number;
}
