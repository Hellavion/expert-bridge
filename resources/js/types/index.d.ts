import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

// Admin Models
export interface Expert {
    id: number;
    name: string;
    telegram_login: string | null;
    comment: string | null;
    commission_percent: string;
    expert_bonus: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    curators_count?: number;
    products_count?: number;
    promocodes_count?: number;
}

export interface Curator {
    id: number;
    expert_id: number;
    name: string;
    telegram_login: string | null;
    curator_bonus: string | null;
    comment: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    expert?: Expert;
    promocodes_count?: number;
}

export interface Product {
    id: number;
    expert_id: number;
    description: string;
    price: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    expert?: Expert;
}

export interface Promocode {
    id: number;
    expert_id: number;
    curator_id: number | null;
    code: string;
    usage_count: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    expert?: Expert;
    curator?: Curator;
}

export interface Client {
    id: number;
    promocode_id: number;
    telegram_login: string;
    is_paid: boolean;
    created_at: string;
    updated_at: string;
    promocode?: Promocode;
}
