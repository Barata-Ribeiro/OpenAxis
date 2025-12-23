export interface Notification<T = Record<string, unknown>> {
    id: string;
    type: string;
    notifiable_type: string;
    notifiable_id: number;
    data: T;
    read_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface NotificationSummary<T = Record<string, unknown>> {
    latest: Notification<T>[];
    unread_count: number;
    total_count: number;
}
