import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
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
export type MessageType = 'text' | 'file' | 'image' | 'audio'; // extend as needed

export interface ChatMessage {
    id: number;
    content: string;
    sender: User;
    timestamp: Date;
    type: MessageType;
    file?: File; // optional, only present if type is 'file', 'image', etc.
}

export type BreadcrumbItemType = BreadcrumbItem;
