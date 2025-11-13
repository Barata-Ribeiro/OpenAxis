import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';
import { UserWithRelations } from './application/user';

export interface Auth {
    user: UserWithRelations;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    flash: Record<'success' | 'error' | 'info' | 'warning', string | null>;
    [key: string]: unknown;
}

export interface Link {
    url?: string;
    label: string;
    page?: number;
    active: boolean;
}

export interface PaginationMeta<T> {
    current_page: number;
    data: T;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Link[];
    next_page_url?: string;
    path: string;
    per_page: number;
    prev_page_url?: string;
    to: number;
    total: number;
}

export interface ScrollMeta<T> {
    data: T[];
    path: string;
    per_page: number;
    next_cursor?: string;
    next_page_url?: string;
    prev_cursor?: string;
    prev_page_url?: string;
}
