export enum ClientType {
    INDIVIDUAL = 'individual',
    COMPANY = 'company',
}

export const CLIENT_TYPE_LABELS: Record<ClientType, string> = {
    [ClientType.INDIVIDUAL]: 'Individual',
    [ClientType.COMPANY]: 'Company',
};

export function clientTypeLabel(type: ClientType): string {
    return CLIENT_TYPE_LABELS[type] ?? type;
}
