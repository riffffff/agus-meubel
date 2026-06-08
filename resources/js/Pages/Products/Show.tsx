import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Product, ShopSetting } from '@/types/mebel';

interface ProductShowProps {
    product: Product;
    shopSettings: ShopSetting;
}

export default function Show({ product, shopSettings }: ProductShowProps) {
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    // Find primary image or use first one as default main image
    const primaryImage = product.images?.find(img => img.is_primary) || product.images?.[0];
    const defaultImgUrl = primaryImage 
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/') ? primaryImage.url : `/storage/${primaryImage.url}`)
        : 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800';

    const [activeImageUrl, setActiveImageUrl] = useState<string>(defaultImgUrl);

    // Format Price to Rupiah
    const formatRupiah = (number: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(number);
    };

    const formattedPrice = formatRupiah(product.price);

    // Build WhatsApp Link
    const buildWhatsAppUrl = () => {
        let text = waTemplate
            ? waTemplate
                .replace('{product_name}', product.name)
                .replace('{product_price}', formattedPrice)
            : `Halo, saya tertarik dengan produk ${product.name} seharga ${formattedPrice}. Apakah masih tersedia?`;

        return `https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`;
    };

    return (
        <PublicLayout>
            <Head>
                <title>{product.name} | Mebel Jati Jepara Premium</title>
                <meta name="description" content={product.short_description} />
            </Head>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
                
                {/* Breadcrumbs */}
                <nav className="text-xs sm:text-sm text-stone-500 mb-8 flex items-center gap-2" aria-label="Breadcrumb">
                    <Link href={route('home')} className="hover:text-amber-900 transition">Beranda</Link>
                    <span>/</span>
                    <Link href={route('products.index')} className="hover:text-amber-900 transition">Katalog</Link>
                    <span>/</span>
                    <span className="text-stone-850 font-bold truncate">{product.name}</span>
                </nav>

                {/* Product Section Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    
                    {/* Left: Gallery Section (Inertia Image Lazy Load) */}
                    <div className="lg:col-span-7 space-y-4">
                        {/* Main Image Frame */}
                        <div className="aspect-square bg-stone-100 rounded-2xl overflow-hidden border border-stone-200/50 shadow-sm relative">
                            <img
                                src={activeImageUrl}
                                alt={product.name}
                                className="w-full h-full object-cover object-center"
                            />
                            
                            {/* Stock Status Badge */}
                            <div className="absolute top-4 left-4">
                                {product.stock_status === 'ready_stock' ? (
                                    <span className="bg-emerald-800 text-stone-100 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-lg shadow-sm">
                                        Ready Stock
                                    </span>
                                ) : (
                                    <span className="bg-amber-700 text-stone-100 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-lg shadow-sm">
                                        Pre Order
                                    </span>
                                )}
                            </div>
                        </div>

                        {/* Thumbnails Gallery */}
                        {product.images && product.images.length > 1 && (
                            <div className="grid grid-cols-5 gap-3">
                                {product.images.map((img) => {
                                    const imgUrl = img.url.startsWith('http') || img.url.startsWith('/') ? img.url : `/storage/${img.url}`;
                                    const isActive = activeImageUrl === imgUrl;
                                    return (
                                        <button
                                            key={img.id}
                                            onClick={() => setActiveImageUrl(imgUrl)}
                                            className={`aspect-square rounded-xl overflow-hidden border bg-stone-50 transition-all duration-200 ${
                                                isActive 
                                                    ? 'border-amber-900 ring-2 ring-amber-900/15 scale-[0.98]' 
                                                    : 'border-stone-200/80 hover:border-stone-400'
                                            }`}
                                        >
                                            <img
                                                src={imgUrl}
                                                alt={`${product.name} Gallery`}
                                                className="w-full h-full object-cover object-center"
                                                loading="lazy"
                                            />
                                        </button>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* Right: Product Details Section */}
                    <div className="lg:col-span-5 space-y-6">
                        <div className="space-y-2">
                            <span className="text-emerald-800 font-extrabold uppercase tracking-wider text-[10px] sm:text-xs block">Premium Wood Craft</span>
                            <h1 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-950 tracking-tight leading-tight">
                                {product.name}
                            </h1>
                        </div>

                        <div className="p-5 bg-stone-100 border border-stone-200/50 rounded-2xl space-y-2">
                            <span className="text-xs text-stone-500 uppercase tracking-wider font-semibold">Harga Penawaran</span>
                            <div className="text-2xl sm:text-3xl font-extrabold text-amber-950">
                                {formattedPrice}
                            </div>
                        </div>

                        <div className="space-y-4">
                            <h3 className="font-bold text-stone-900 text-sm uppercase tracking-wider border-b border-stone-200 pb-2">Deskripsi Singkat</h3>
                            <p className="text-stone-600 text-sm sm:text-base leading-relaxed">
                                {product.short_description}
                            </p>
                        </div>

                        <div className="pt-4 flex gap-4">
                            <a
                                href={buildWhatsAppUrl()}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="flex-grow inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl text-stone-100 font-bold text-sm sm:text-base shadow-lg transition duration-300 hover:scale-[1.01]"
                                style={{ backgroundColor: '#075E54' }}
                            >
                                <svg className="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.731-1.456L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.625 1.45 5.436.002 9.852-4.41 9.855-9.852.002-2.637-1.023-5.114-2.883-6.976C16.381 1.916 13.91 .89 11.272.89c-5.433 0-9.85 4.417-9.853 9.856 0 1.562.488 3.09 1.41 4.481L1.8 20.353l5.05-1.325c-.244-.132-.244-.132.207.126z"/>
                                </svg>
                                Hubungi Pengrajin via WhatsApp
                            </a>
                        </div>
                    </div>

                </div>

                {/* Long Description Section */}
                {product.description && (
                    <div className="mt-16 pt-12 border-t border-stone-200/80">
                        <h2 className="text-xl sm:text-2xl font-extrabold text-stone-900 mb-6 uppercase tracking-wide border-l-4 border-amber-900 pl-4">
                            Detail Spesifikasi & Deskripsi
                        </h2>
                        <div 
                            className="prose prose-stone max-w-none text-stone-600 leading-relaxed text-sm sm:text-base space-y-4"
                            dangerouslySetInnerHTML={{ __html: product.description }}
                        />
                    </div>
                )}

            </div>
        </PublicLayout>
    );
}
