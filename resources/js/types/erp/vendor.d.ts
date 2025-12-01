import { User } from '../application/user';

export interface Vendor {
    id: number;
    user_id: number;
    first_name: string;
    last_name: string;
    full_name?: string;
    date_of_birth: string;
    phone_number: string;
    commission_rate: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface VendorWithRelations extends Vendor {
    user: User;
}
