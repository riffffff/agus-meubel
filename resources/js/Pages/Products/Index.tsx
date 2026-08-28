import React from 'react';
import { Head, router } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import ProductCard from '@/Components/Public/ProductCard';
import Pagination from '@/Components/Public/Pagination';
import ScrollReveal from '@/Components/Public/ScrollReveal';
import { Product, ShopSetting } from '@/types/mebel';
import { RefreshCcw, Search, Sofa } from 'lucide-react';

interface ProductsIndexProps {
    products: {
        data: Product[];
        links: any[];
    };
    shopSettings: ShopSetting;
    filters: {
        stock_status?: string;
        sort?: string;
    };
}

export default function Index({ products, shopSettings, filters }: ProductsIndexProps) {
    const waNumber   = shopSettings?.whatsapp_number   || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    const productsData    = Array.isArray(products?.data)  ? products.data  : [];
    const productsLinks   = Array.isArray(products?.links) ? products.links : [];
    const safeFilters     = filters && typeof filters === 'object' ? filters : {};
    const filterStockStatus = typeof safeFilters.stock_status === 'string' ? safeFilters.stock_status : '';
    const filterSort        = typeof safeFilters.sort === 'string' ? safeFilters.sort : '';
    const hasActiveFilters  = Boolean(filterStockStatus) || (Boolean(filterSort) && filterSort !== 'newest');

    const handleFilterChange = (key: string, value: string) => {
        const newFilters: Record<string, string> = { ...safeFilters } as Record<string, string>;
        newFilters[key] = value;
        if (!value) delete newFilters[key];
        router.get(route('products.index'), newFilters, { preserveState: true, replace: true });
    };

    return (
        <PublicLayout>
            <Head>
                <title>Katalog Produk Mebel Jati Jepara | Agus Mebel</title>
                <meta name="description" content="Jelajahi koleksi lengkap furniture kayu jati premium kami. Tersedia kursi tamu, meja makan, tempat tidur, lemari, dll." />
            </Head>

            {/* Hero Banner — mahogany + wood texture */}
            <div className="bg-mahogany-900 py-16 sm:py-20 text-center text-white relative overflow-hidden wood-texture">
                <div className="absolute inset-0 bg-gradient-to-b from-mahogany-950/60 to-mahogany-900/30 pointer-events-none" />
                <div className="relative max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
                    <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">Katalog Produk</h1>
                    <p className="text-sm sm:text-base text-mahogany-200 max-w-2xl mx-auto leading-relaxed">
                        Pilih dari ratusan desain furniture jepara berkualitas tinggi untuk melengkapi keindahan setiap ruangan rumah Anda.
                    </p>
                </div>
            </div>

            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="flex flex-col md:flex-row gap-8 items-start">

                    {/* Sidebar Filter */}
                    <ScrollReveal direction="left" className="w-full md:w-64 shrink-0">
                        <div className="bg-white p-6 rounded-2xl border border-mahogany-100 shadow-sm space-y-6">
                            <h3 className="font-bold text-mahogany-900 border-b border-mahogany-100 pb-3 flex items-center gap-2 text-sm uppercase tracking-wider">
                                <Search className="h-4 w-4 text-mahogany-600" />
                                Filter & Urutkan
                            </h3>

                            <div className="space-y-2">
                                <label className="text-xs font-bold text-mahogany-400 uppercase tracking-wider block">Status Stok</label>
                                <select
                                    value={filterStockStatus}
                                    onChange={(e) => handleFilterChange('stock_status', e.target.value)}
                                    className="w-full rounded-xl border border-mahogany-200 text-stone-700 bg-mahogany-50/30 focus:border-mahogany-700 focus:ring-1 focus:ring-mahogany-700/20 text-sm py-2.5 transition outline-none"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="available">Ready Stock</option>
                                    <option value="preorder">Pre Order</option>
                                    <option value="out_of_stock">Kosong</option>
                                </select>
                            </div>

                            <div className="space-y-2">
                                <label className="text-xs font-bold text-mahogany-400 uppercase tracking-wider block">Urutkan</label>
                                <select
                                    value={filterSort || 'newest'}
                                    onChange={(e) => handleFilterChange('sort', e.target.value)}
                                    className="w-full rounded-xl border border-mahogany-200 text-stone-700 bg-mahogany-50/30 focus:border-mahogany-700 focus:ring-1 focus:ring-mahogany-700/20 text-sm py-2.5 transition outline-none"
                                >
                                    <option value="newest">Terbaru</option>
                                    <option value="price_low">Harga Terendah</option>
                                    <option value="price_high">Harga Tertinggi</option>
                                </select>
                            </div>

                            {hasActiveFilters && (
                                <button
                                    onClick={() => router.get(route('products.index'))}
                                    className="w-full py-2 px-4 rounded-xl border border-mahogany-700 text-mahogany-700 hover:bg-mahogany-50 text-xs font-bold transition flex items-center justify-center gap-1.5"
                                >
                                    <RefreshCcw className="h-3.5 w-3.5" />
                                    Bersihkan Filter
                                </button>
                            )}
                        </div>
                    </ScrollReveal>

                    {/* Grid Produk */}
                    <div className="grow w-full">
                        {productsData.length > 0 ? (
                            <>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                                    {productsData.map((product, index) => (
                                        product ? (
                                            <ScrollReveal key={product.id ?? index} delay={(index % 3) * 150} direction="up">
                                                <ProductCard
                                                    product={product}
                                                    whatsappNumber={waNumber}
                                                    whatsappTemplate={waTemplate}
                                                />
                                            </ScrollReveal>
                                        ) : null
                                    ))}
                                </div>
                                <Pagination links={productsLinks} />
                            </>
                        ) : (
                            <ScrollReveal direction="none">
                                <div className="bg-white p-16 text-center rounded-2xl border border-mahogany-100 shadow-sm max-w-md mx-auto space-y-4">
                                    <Sofa className="mx-auto h-12 w-12 text-mahogany-700" />
                                    <h3 className="text-lg font-bold text-stone-900">Produk Tidak Ditemukan</h3>
                                    <p className="text-stone-500 text-sm leading-relaxed">
                                        Maaf, tidak ada produk yang sesuai dengan filter Anda. Coba ubah atau bersihkan filter.
                                    </p>
                                    <button
                                        onClick={() => router.get(route('products.index'))}
                                        className="px-5 py-2.5 bg-mahogany-800 hover:bg-mahogany-700 text-white font-bold rounded-xl shadow-sm transition text-xs"
                                    >
                                        Lihat Semua Produk
                                    </button>
                                </div>
                            </ScrollReveal>
                        )}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
