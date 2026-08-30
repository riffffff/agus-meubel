import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Product, ShopSetting } from '@/types/mebel';
import { MessageCircle, ShoppingCart, UserPlus, LogIn, Lock, Minus, Plus, AlertCircle } from 'lucide-react';
import { formatRupiah } from '@/lib/utils';
import AuthModal from '@/Components/Public/AuthModal';

interface ProductShowProps {
    product: Product;
    shopSettings: ShopSetting;
}

export default function Show({ product, shopSettings }: ProductShowProps) {
    const { auth } = usePage().props as any;
    const user      = auth?.user;
    const isLoggedIn = !!user;
    const waNumber   = shopSettings?.whatsapp_number   || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
    const [authModalMode, setAuthModalMode] = useState<'login' | 'register'>('login');

    const openAuthModal = (mode: 'login' | 'register') => {
        setAuthModalMode(mode);
        setIsAuthModalOpen(true);
    };

    const productName        = product?.name             ?? 'Produk';
    const productShortDesc   = product?.short_description ?? '';
    const productDescription = product?.description       ?? '';
    const productPrice       = product?.price             ?? 0;
    const productStockStatus = product?.stock_status      ?? 'preorder';
    const productImages      = Array.isArray(product?.images) ? product.images : [];
    const productId          = product?.id;
    const isOutOfStock       = productStockStatus === 'out_of_stock';

    const primaryImage = productImages.find(img => img && img.is_primary) || productImages[0];
    const defaultImgUrl = primaryImage?.url
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/')
            ? primaryImage.url
            : `/storage/${primaryImage.url}`)
        : 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800';

    const [activeImageUrl, setActiveImageUrl] = useState<string>(defaultImgUrl);
    const [qty, setQty] = useState<number>(1);
    const [isMounted, setIsMounted] = useState<boolean>(false);

    useEffect(() => {
        const t = setTimeout(() => setIsMounted(true), 30);
        return () => clearTimeout(t);
    }, []);

    const { data: _cartData, setData, post: cartPost, processing, errors: cartErrors, reset: cartReset } = useForm({
        product_id: productId ?? 0,
        quantity: 1,
    });

    const addToCartHandler = (e: React.FormEvent) => {
        e.preventDefault();
        setData('product_id', productId ?? 0);
        setData('quantity', Math.max(1, qty));
        cartPost(route('cart.store'), { onFinish: () => cartReset() });
    };

    const formattedPrice = formatRupiah(productPrice);

    const buildWhatsAppUrl = () => {
        const text = waTemplate
            ? waTemplate.replaceAll('{product_name}', productName).replaceAll('{product_price}', formattedPrice)
            : `Halo, saya tertarik dengan produk ${productName} seharga ${formattedPrice}. Apakah masih tersedia?`;
        return `https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`;
    };

    return (
        <PublicLayout>
            <Head>
                <title>{`${productName} | ${shopSettings?.shop_name || 'Agus Mebel Jepara'}`}</title>
                <meta name="description" content={productShortDesc || 'Furniture kayu jati premium dari pengrajin Jepara.'} />
            </Head>

            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

                {/* Breadcrumb */}
                <nav className="text-xs sm:text-sm text-stone-400 mb-6 flex items-center gap-2" aria-label="Breadcrumb">
                    <Link href={route('home')} className="hover:text-mahogany-700 transition">Beranda</Link>
                    <span>/</span>
                    <Link href={route('products.index')} className="hover:text-mahogany-700 transition">Katalog</Link>
                    <span>/</span>
                    <span className="text-stone-900 font-bold truncate">{productName}</span>
                </nav>

                <div className={`grid grid-cols-1 lg:grid-cols-12 gap-10 transition-opacity duration-400 ease-out ${isMounted ? 'opacity-100' : 'opacity-0'}`}>

                    {/* ── Galeri Gambar ─────────────────────────── */}
                    <div className="lg:col-span-7 space-y-4">
                        <div className="aspect-square bg-mahogany-50 rounded-2xl overflow-hidden border border-mahogany-100 shadow-sm relative">
                            <img src={activeImageUrl} alt={productName} className="w-full h-full object-cover object-center" />

                            {/* Badge status */}
                            <div className="absolute top-4 left-4">
                                {productStockStatus === 'available' ? (
                                    <span className="bg-emerald-800 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md shadow-xs">Ready Stock</span>
                                ) : productStockStatus === 'out_of_stock' ? (
                                    <span className="bg-rose-800 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md shadow-xs">Stok Habis</span>
                                ) : (
                                    <span className="bg-mahogany-700 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md shadow-xs">Pre Order</span>
                                )}
                            </div>
                        </div>

                        {/* Thumbnail */}
                        {productImages.length > 1 && (
                            <div className="grid grid-cols-5 gap-3">
                                {productImages.map((img) => {
                                    if (!img?.url) return null;
                                    const imgUrl = img.url.startsWith('http') || img.url.startsWith('/') ? img.url : `/storage/${img.url}`;
                                    const isActive = activeImageUrl === imgUrl;
                                    return (
                                        <button
                                            key={img.id ?? Math.random()}
                                            onClick={() => setActiveImageUrl(imgUrl)}
                                            className={`aspect-square rounded-xl overflow-hidden border bg-mahogany-50 transition-all duration-200 ${
                                                isActive
                                                    ? 'border-mahogany-700 ring-2 ring-mahogany-700/20 scale-[0.98]'
                                                    : 'border-mahogany-100 hover:border-mahogany-300'
                                            }`}
                                        >
                                            <img src={imgUrl} alt={`${productName} Gallery`} className="w-full h-full object-cover" loading="lazy" />
                                        </button>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* ── Panel Pembelian ────────────────────────── */}
                    <div className="lg:col-span-5 space-y-6">

                        {/* Judul */}
                        <div className="space-y-1.5">
                            <span className="text-mahogany-600 font-extrabold uppercase tracking-wider text-xs block">Mebel Jati Premium</span>
                            <h1 className="text-2xl sm:text-3xl font-extrabold text-stone-950 tracking-tight leading-tight">
                                {productName}
                            </h1>
                        </div>

                        {/* Harga */}
                        <div className="p-4 sm:p-5 bg-mahogany-50 border border-mahogany-200/60 rounded-2xl space-y-1">
                            <span className="text-xs text-mahogany-500 uppercase tracking-wider font-semibold">Harga Produk</span>
                            <div className="text-2xl sm:text-3xl font-extrabold text-mahogany-900">
                                {formattedPrice}
                            </div>
                        </div>

                        {/* Deskripsi singkat */}
                        <div className="space-y-2">
                            <h3 className="font-bold text-stone-800 text-xs uppercase tracking-wider border-b border-mahogany-100 pb-2">Keterangan Singkat</h3>
                            <p className="text-stone-600 text-sm leading-relaxed">
                                {productShortDesc || 'Produk buatan pengrajin Jepara menggunakan kayu jati pilihan.'}
                            </p>
                        </div>

                        {/* CTA — tidak login */}
                        {!isLoggedIn ? (
                            <div className="pt-4 space-y-3">
                                <div className="p-5 rounded-2xl border border-mahogany-100 bg-mahogany-50/50">
                                    <div className="flex items-start gap-3">
                                        <div className="p-2 rounded-xl bg-mahogany-100 text-mahogany-800 shrink-0">
                                            <Lock className="h-5 w-5" />
                                        </div>
                                        <div className="space-y-3">
                                            <div>
                                                <p className="text-sm font-bold text-stone-900">Masuk untuk Memesan ke Keranjang</p>
                                                <p className="text-xs text-stone-500 mt-1 leading-relaxed">
                                                    Silakan masuk atau buat akun untuk menambahkan ke keranjang.
                                                </p>
                                            </div>
                                            <div className="flex flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => openAuthModal('login')}
                                                    className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-mahogany-800 text-white text-xs font-bold hover:bg-mahogany-700 transition"
                                                >
                                                    <LogIn className="h-3.5 w-3.5" />
                                                    <span>Masuk</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => openAuthModal('register')}
                                                    className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-mahogany-200 bg-white text-mahogany-800 text-xs font-bold hover:bg-mahogany-50 transition"
                                                >
                                                    <UserPlus className="h-3.5 w-3.5" />
                                                    <span>Daftar Akun</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a
                                    href={buildWhatsAppUrl()}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-white font-bold text-sm shadow-xs transition hover:opacity-90"
                                    style={{ backgroundColor: '#075E54' }}
                                >
                                    <MessageCircle className="w-4 h-4" />
                                    <span>Tanya / Pesan via WhatsApp</span>
                                </a>
                            </div>
                        ) : (
                            /* CTA — sudah login */
                            <form onSubmit={addToCartHandler} className="pt-4 space-y-4">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end p-4 sm:p-5 rounded-2xl bg-mahogany-50/50 border border-mahogany-100">
                                    <div className="space-y-2.5">
                                        <label className="text-xs font-bold text-stone-600 uppercase tracking-wider">
                                            Jumlah Pembelian
                                        </label>
                                        <div className="inline-flex items-center border border-mahogany-200 rounded-xl overflow-hidden bg-white shadow-sm h-11">
                                            <button
                                                type="button"
                                                onClick={() => setQty((v) => Math.max(1, v - 1))}
                                                className="w-11 h-full flex items-center justify-center text-mahogany-700 hover:bg-mahogany-50 active:bg-mahogany-100 transition disabled:opacity-40 disabled:hover:bg-transparent shrink-0"
                                                disabled={qty <= 1}
                                            >
                                                <Minus className="w-4.5 h-4.5 stroke-[2.5]" />
                                            </button>
                                            <input
                                                type="number"
                                                min={1} max={100}
                                                value={qty}
                                                onChange={(e) => {
                                                    const v = parseInt(e.target.value, 10);
                                                    setQty(Number.isFinite(v) && v > 0 ? Math.min(100, v) : 1);
                                                }}
                                                className="w-16 h-full text-center font-extrabold text-stone-900 text-base border-x border-mahogany-200 focus:outline-none bg-transparent [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            />
                                            <button
                                                type="button"
                                                onClick={() => setQty((v) => Math.min(100, v + 1))}
                                                className="w-11 h-full flex items-center justify-center text-mahogany-700 hover:bg-mahogany-50 active:bg-mahogany-100 transition shrink-0"
                                            >
                                                <Plus className="w-4.5 h-4.5 stroke-[2.5]" />
                                            </button>
                                        </div>
                                    </div>
                                    <div className="flex flex-col items-start sm:items-end justify-end space-y-1">
                                        <span className="text-xs font-semibold text-stone-500 uppercase tracking-wider">
                                            Total Harga
                                        </span>
                                        <span className="text-lg sm:text-xl font-extrabold text-mahogany-900 leading-none">
                                            {formatRupiah((typeof productPrice === 'number' ? productPrice : parseFloat(String(productPrice)) || 0) * qty)}
                                        </span>
                                    </div>
                                </div>

                                {cartErrors?.product_id && (
                                    <div className="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 font-semibold flex items-center gap-2">
                                        <AlertCircle className="h-4 w-4 shrink-0 text-rose-600" />
                                        <span>{cartErrors.product_id}</span>
                                    </div>
                                )}

                                <div className="flex flex-col sm:flex-row gap-3">
                                    <button
                                        type="submit"
                                        disabled={isOutOfStock || processing}
                                        className={`grow inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm shadow-xs transition ${
                                            isOutOfStock
                                                ? 'bg-stone-200 text-stone-500 cursor-not-allowed'
                                                : 'bg-mahogany-800 text-white hover:bg-mahogany-700 active:scale-[0.98]'
                                        }`}
                                    >
                                        <ShoppingCart className="w-4 h-4" />
                                        <span>{isOutOfStock ? 'Stok Habis' : processing ? 'Menambahkan...' : 'Tambah ke Keranjang'}</span>
                                    </button>

                                    <a
                                        href={buildWhatsAppUrl()}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="grow inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-white font-bold text-sm shadow-xs transition hover:opacity-90"
                                        style={{ backgroundColor: '#075E54' }}
                                    >
                                        <MessageCircle className="w-4 h-4" />
                                        <span>Pesan via WhatsApp</span>
                                    </a>
                                </div>
                            </form>
                        )}
                    </div>
                </div>

                {/* Deskripsi lengkap */}
                {productDescription && (
                    <div className="mt-14 pt-10 border-t border-mahogany-100">
                        <h2 className="text-lg font-extrabold text-stone-900 mb-4 border-l-4 border-mahogany-700 pl-3">
                            Deskripsi Produk
                        </h2>
                        <div
                            className="prose prose-stone max-w-none text-stone-600 leading-relaxed text-sm space-y-3"
                            dangerouslySetInnerHTML={{ __html: productDescription }}
                        />
                    </div>
                )}
            </div>

            <AuthModal
                isOpen={isAuthModalOpen}
                onClose={() => setIsAuthModalOpen(false)}
                initialMode={authModalMode}
            />
        </PublicLayout>
    );
}
