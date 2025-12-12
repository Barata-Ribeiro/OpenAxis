import type { Address } from '@/types/application/address';
import type { PartnerTypes } from '@/types/erp/erp-enums';

export interface Partner {
    id: number;
    type: PartnerTypes;
    name: string;
    email: string;
    phone_number: string;
    identification: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface PartnerWithRelations extends Partner {
    addresses: Address[];
}
