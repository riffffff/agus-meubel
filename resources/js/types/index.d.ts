import { Config } from 'ziggy-js';
import { StockStatus } from './mebel';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface ShopSettingsShared {
    id?: number;
    shop_name?: string | null;
    logo?: string | null;
    logo_dark?: string | null;
    favicon?: string | null;
    logo_url?: string;
    logo_dark_url?: string;
    favicon_url?: string;
    address?: string | null;
    whatsapp_number: string;
    whatsapp_template: string;
    operating_hours?: string | null;
    hero_banner_text_1?: string | null;
    hero_banner_text_2?: string | null;
    hero_banner_bg?: string | null;
    shipping_areas?: string[] | null;
    shipping_estimate_days?: string | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
    flash: {
        success: string | null;
        error: string | null;
    };
    shopSettings: ShopSettingsShared;
};

export type { StockStatus };
