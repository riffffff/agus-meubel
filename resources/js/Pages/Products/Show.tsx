import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Product, ShopSetting } from '@/types/mebel';
import { MessageCircle } from 'lucide-react';

interface ProductShowProps {
    product: Product;
    shopSettings: ShopSetting;
}

export default function Show({ product, shopSettings }: ProductShowProps) {
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    const productName = product?.name ?? 'Produk';
    const productShortDesc = product?.short_description ?? '';
    const productDescription = product?.description ?? '';
    const productPrice = product?.price ?? 0;
    const productStockStatus = product?.stock_status ?? 'preorder';
    const productImages = Array.isArray(product?.images) ? product.images : [];

    const primaryImage = productImages.find(img => img && img.is_primary) || productImages[0];
    const defaultImgUrl = primaryImage?.url
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/')
            ? primaryImage.url
            : `/storage/${primaryImage.url}`)
        : 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800';

    const [activeImageUrl, setActiveImageUrl] = useState<string>(defaultImgUrl);

    const formatRupiah = (number: number) => {
        const num = typeof number === 'number' && !isNaN(number) ? number : 0;
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(num);
    };

    const formattedPrice = formatRupiah(productPrice);

    const buildWhatsAppUrl = () => {
        let text = waTemplate
            ? waTemplate
                .replaceAll('{product_name}', productName)
                .replaceAll('{product_price}', formattedPrice)
            : `Halo, saya tertarik dengan produk ${productName} seharga ${formattedPrice}. Apakah masih tersedia?`;

        return `https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`;
    };

    const pageTitle = `${productName} | Mebel Jati Jepara Premium`;
    const metaDescription = productShortDesc || 'Produk furniture kayu jati premium berkualitas tinggi langsung dari pengrajin Jepara.';

    return (
        <PublicLayout>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={metaDescription} />
            </Head>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">

                <nav className="text-xs sm:text-sm text-stone-500 mb-8 flex items-center gap-2" aria-label="Breadcrumb">
                    <Link href={route('home')} className="hover:text-amber-900 transition">Beranda</Link>
                    <span>/</span>
                    <Link href={route('products.index')} className="hover:text-amber-900 transition">Katalog</Link>
                    <span>/</span>
                    <span className="text-stone-900 font-bold truncate">{productName}</span>
                </nav>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">

                    <div className="lg:col-span-7 space-y-4">
                        <div className="aspect-square bg-stone-100 rounded-2xl overflow-hidden border border-stone-200/50 shadow-sm relative">
                            <img
                                src={activeImageUrl}
                                alt={productName}
                                className="w-full h-full object-cover object-center"
                            />

                            <div className="absolute top-4 left-4">
                                {productStockStatus === 'available' ? (
                                    <span className="bg-emerald-800 text-stone-100 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-lg shadow-sm">
                                        Ready Stock
                                    </span>
                                ) : productStockStatus === 'out_of_stock' ? (
                                    <span className="bg-rose-800 text-stone-100 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-lg shadow-sm">
                                        Sold Out
                                    </span>
                                ) : (
                                    <span className="bg-amber-700 text-stone-100 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-lg shadow-sm">
                                        Pre Order
                                    </span>
                                )}
                            </div>
                        </div>

                        {productImages.length > 1 && (
                            <div className="grid grid-cols-5 gap-3">
                                {productImages.map((img) => {
                                    if (!img || !img.url) return null;
                                    const imgUrl = img.url.startsWith('http') || img.url.startsWith('/') ? img.url : `/storage/${img.url}`;
                                    const isActive = activeImageUrl === imgUrl;
                                    return (
                                        <button
                                            key={img.id ?? Math.random()}
                                            onClick={() => setActiveImageUrl(imgUrl)}
                                            className={`aspect-square rounded-xl overflow-hidden border bg-stone-50 transition-all duration-200 ${
                                                isActive
                                                    ? 'border-amber-900 ring-2 ring-amber-900/15 scale-[0.98]'
                                                    : 'border-stone-200/80 hover:border-stone-400'
                                            }`}
                                        >
                                            <img
                                                src={imgUrl}
                                                alt={`${productName} Gallery`}
                                                className="w-full h-full object-cover object-center"
                                                loading="lazy"
                                            />
                                        </button>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    <div className="lg:col-span-5 space-y-6">
                        <div className="space-y-2">
                            <span className="text-emerald-800 font-extrabold uppercase tracking-wider text-[10px] sm:text-xs block">Premium Wood Craft</span>
                            <h1 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-950 tracking-tight leading-tight">
                                {productName}
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
                                {productShortDesc || '-'}
                            </p>
                        </div>

                        <div className="pt-4 flex gap-4">
                            <a
                                href={buildWhatsAppUrl()}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="grow inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl text-stone-100 font-bold text-sm sm:text-base shadow-lg transition duration-300 hover:scale-[1.01]"
                                style={{ backgroundColor: '#075E54' }}
                            >
                                <MessageCircle className="w-5 h-5" />
                                Hubungi Pengrajin via WhatsApp
                            </a>
                        </div>
                    </div>

                </div>

                {productDescription && (
                    <div className="mt-16 pt-12 border-t border-stone-200/80">
                        <h2 className="text-xl sm:text-2xl font-extrabold text-stone-900 mb-6 uppercase tracking-wide border-l-4 border-amber-900 pl-4">
                            Detail Spesifikasi & Deskripsi
                        </h2>
                        <div
                            className="prose prose-stone max-w-none text-stone-600 leading-relaxed text-sm sm:text-base space-y-4"
                            dangerouslySetInnerHTML={{ __html: productDescription }}
                        />
                    </div>
                )}

            </div>
        </PublicLayout>
    );
}
