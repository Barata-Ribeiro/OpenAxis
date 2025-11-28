import type { Address } from '@/types/application/address';
import { ClientType } from './erp-enums';

export interface Client {
    id: number;
    name: string;
    email: string;
    phone_number: string;
    identification: string;
    client_type: ClientType;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface ClientWithRelations extends Client {
    addresses: Address[];
}
