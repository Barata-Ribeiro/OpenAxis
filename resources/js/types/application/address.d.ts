import type { AddressTypes } from './enums';

export interface Address {
    id: number;
    type: AddressTypes;
    label: string | null;
    street: string;
    number: string;
    complement: string;
    neighborhood: string;
    city: string;
    state: string;
    postal_code: string;
    country: string;
    is_primary: boolean;
}
