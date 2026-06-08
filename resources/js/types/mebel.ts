export interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;       // rich text / konten panjang
  short_description: string; // untuk kartu di list & halaman utama
  price: number;             // integer, dalam Rupiah
  stock_status: 'ready_stock' | 'pre_order';
  images: ProductImage[];
  created_at: string;
}

export interface ProductImage {
  id: number;
  url: string;
  is_primary: boolean;  // gambar utama untuk thumbnail kartu
}

export interface Article {
  id: number;
  title: string;
  slug: string;
  content: string;   // rich text
  excerpt: string;   // ringkasan pendek untuk kartu & hero section
  image: string;     // URL gambar utama (sudah dikonversi ke .webp)
  is_hero: boolean;
  published_at: string;
}

export interface Review {
  id: number;
  name: string;
  city: string;
  rating: 1 | 2 | 3 | 4 | 5;
  review: string;
}

export interface ShopSetting {
  id?: number;
  whatsapp_number: string;   // format: 628xxxxxxxxx
  whatsapp_template: string; // template pesan dengan placeholder
}
