import React from 'react';
import { Head, router } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import ProductCard from '@/Components/Public/ProductCard';
import Pagination from '@/Components/Public/Pagination';
import { Product, ShopSetting } from '@/types/mebel';

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
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    const handleFilterChange = (key: string, value: string) => {
        const newFilters = { ...filters, [key]: value };
        
        // Remove empty values
        if (!value) {
            delete newFilters[key as keyof typeof newFilters];
        }

        router.get(route('products.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <PublicLayout>
            <Head>
                <title>Katalog Produk Mebel Jati Jepara | Agus Mebel</title>
                <meta name="description" content="Jelajahi koleksi lengkap furniture kayu jati premium kami. Tersedia kursi tamu, meja makan, tempat tidur, lemari, dll. Kualitas terjamin langsung dari Jepara." />
            </Head>

            {/* Header Banner */}
            <div className="bg-stone-900 py-16 sm:py-20 text-center text-white relative overflow-hidden">
                <div className="absolute inset-0 bg-[radial-gradient(#3e2723_1px,transparent_1px)] [background-size:16px_16px] opacity-15"></div>
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
                    <span className="text-amber-500 font-extrabold uppercase tracking-widest text-[10px] bg-amber-950/40 px-3 py-1 rounded-md">Koleksi Jati Asli</span>
                    <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">Katalog Produk</h1>
                    <p className="text-sm sm:text-base text-stone-300 max-w-2xl mx-auto leading-relaxed">Pilih dari ratusan desain furniture jepara berkualitas tinggi untuk melengkapi keindahan setiap ruangan rumah Anda.</p>
                </div>
            </div>

            {/* Catalog Grid + Filters */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="flex flex-col md:flex-row gap-8 items-start">
                    
                    {/* Filters Sidebar (Desktop) / Dropdowns (Mobile) */}
                    <div className="w-full md:w-64 bg-white p-6 rounded-2xl border border-stone-200/60 shadow-sm space-y-6 flex-shrink-0">
                        <h3 className="font-bold text-stone-900 border-b border-stone-150 pb-3 flex items-center gap-2 text-sm uppercase tracking-wider">
                            <span>🔍</span> Filter & Urutkan
                        </h3>

                        {/* Filter by Stock Status */}
                        <div className="space-y-2">
                            <label className="text-xs font-bold text-stone-400 uppercase tracking-wider block">Status Stok</label>
                            <select
                                value={filters.stock_status || ''}
                                onChange={(e) => handleFilterChange('stock_status', e.target.value)}
                                className="w-full rounded-xl border-stone-200 text-stone-700 bg-stone-50/50 focus:border-amber-900 focus:ring-amber-900/10 text-sm py-2.5 transition"
                            >
                                <option value="">Semua Status</option>
                                <option value="ready_stock">Ready Stock</option>
                                <option value="pre_order">Pre Order</option>
                            </select>
                        </div>

                        {/* Sort products */}
                        <div className="space-y-2">
                            <label className="text-xs font-bold text-stone-400 uppercase tracking-wider block">Urutkan</label>
                            <select
                                value={filters.sort || 'newest'}
                                onChange={(e) => handleFilterChange('sort', e.target.value)}
                                className="w-full rounded-xl border-stone-200 text-stone-700 bg-stone-50/50 focus:border-amber-900 focus:ring-amber-900/10 text-sm py-2.5 transition"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="price_low">Harga Terendah</option>
                                <option value="price_high">Harga Tertinggi</option>
                            </select>
                        </div>

                        {/* Reset Filters */}
                        {(filters.stock_status || filters.sort) && (
                            <button
                                onClick={() => router.get(route('products.index'))}
                                className="w-full py-2 px-4 rounded-xl border border-amber-900 text-amber-900 hover:bg-amber-50 text-xs font-bold transition flex items-center justify-center gap-1.5"
                            >
                                🔄 Bersihkan Filter
                            </button>
                        )}
                    </div>

                    {/* Products Catalog Grid */}
                    <div className="flex-grow w-full">
                        {products.data && products.data.length > 0 ? (
                            <>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                                    {products.data.map((product) => (
                                        <ProductCard
                                            key={product.id}
                                            product={product}
                                            whatsappNumber={waNumber}
                                            whatsappTemplate={waTemplate}
                                        />
                                    ))}
                                </div>
                                <Pagination links={products.links} />
                            </>
                        ) : (
                            <div className="bg-white p-16 text-center rounded-2xl border border-stone-200/60 shadow-sm max-w-md mx-auto space-y-4">
                                <span className="text-5xl block">🪑</span>
                                <h3 className="text-lg font-bold text-stone-900">Produk Tidak Ditemukan</h3>
                                <p className="text-stone-500 text-sm leading-relaxed">
                                    Maaf, tidak ada produk yang sesuai dengan kriteria pencarian Anda. Silakan ubah filter status atau urutan Anda.
                                </p>
                                <button
                                    onClick={() => router.get(route('products.index'))}
                                    className="px-5 py-2.5 bg-amber-900 hover:bg-amber-800 text-stone-100 font-bold rounded-xl shadow-md transition text-xs"
                                >
                                    Lihat Semua Produk
                                </button>
                            </div>
                        )}
                    </div>

                </div>
            </div>
        </PublicLayout>
    );
}
