import React, { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import ScrollReveal from '@/Components/Public/ScrollReveal';
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
    const user = auth?.user;
    const isLoggedIn = !!user;
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
    const [authModalMode, setAuthModalMode] = useState<'login' | 'register'>('login');

    const openAuthModal = (mode: 'login' | 'register') => {
        setAuthModalMode(mode);
        setIsAuthModalOpen(true);
    };

    const productName = product?.name ?? 'Produk';
    const productShortDesc = product?.short_description ?? '';
    const productDescription = product?.description ?? '';
    const productPrice = product?.price ?? 0;
    const productStockStatus = product?.stock_status ?? 'preorder';
    const productImages = Array.isArray(product?.images) ? product.images : [];
    const productId = product?.id;
    const isOutOfStock = productStockStatus === 'out_of_stock';

    const primaryImage = productImages.find(img => img && img.is_primary) || productImages[0];
    const defaultImgUrl = primaryImage?.url
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/')
            ? primaryImage.url
            : `/storage/${primaryImage.url}`)
        : 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800';

    const [activeImageUrl, setActiveImageUrl] = useState<string>(defaultImgUrl);
    const [qty, setQty] = useState<number>(1);

    const {
        data: _cartData,
        setData,
        post: cartPost,
        processing,
        errors: cartErrors,
        reset: cartReset,
    } = useForm({
        product_id: productId ?? 0,
        quantity: 1,
    });

    const addToCartHandler = (e: React.FormEvent) => {
        e.preventDefault();
        setData('product_id', productId ?? 0);
        setData('quantity', Math.max(1, qty));
        cartPost(route('cart.store'), {
            onFinish: () => cartReset(),
        });
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

    const pageTitle = `${productName} | ${shopSettings?.shop_name || 'Agus Mebel Jepara'}`;
    const metaDescription = productShortDesc || 'Produk furniture kayu jati premium berkualitas tinggi langsung dari pengrajin Jepara.';

    return (
        <PublicLayout>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={metaDescription} />
            </Head>

            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
                <nav className="text-xs sm:text-sm text-stone-500 mb-6 flex items-center gap-2" aria-label="Breadcrumb">
                    <Link href={route('home')} className="hover:text-amber-900 transition">Beranda</Link>
                    <span>/</span>
                    <Link href={route('products.index')} className="hover:text-amber-900 transition">Katalog</Link>
                    <span>/</span>
                    <span className="text-stone-900 font-bold truncate">{productName}</span>
                </nav>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <ScrollReveal direction="left" className="lg:col-span-7 space-y-4">
                        <div className="aspect-square bg-stone-100 rounded-2xl overflow-hidden border border-stone-200/60 shadow-xs relative">
                            <img
                                src={activeImageUrl}
                                alt={productName}
                                className="w-full h-full object-cover object-center"
                            />

                            <div className="absolute top-4 left-4">
                                {productStockStatus === 'available' ? (
                                    <span className="bg-emerald-800 text-stone-100 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md shadow-xs">
                                        Ready Stock
                                    </span>
                                ) : productStockStatus === 'out_of_stock' ? (
                                    <span className="bg-rose-800 text-stone-100 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md shadow-xs">
                                        Stok Habis
                                    </span>
                                ) : (
                                    <span className="bg-amber-800 text-stone-100 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md shadow-xs">
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
                    </ScrollReveal>

                    <ScrollReveal direction="right" className="lg:col-span-5 space-y-6">
                        <div className="space-y-1.5">
                            <span className="text-amber-900 font-extrabold uppercase tracking-wider text-xs block">Mebel Jati Premium</span>
                            <h1 className="text-2xl sm:text-3xl font-extrabold text-stone-950 tracking-tight leading-tight">
                                {productName}
                            </h1>
                        </div>

                        <div className="p-4 sm:p-5 bg-amber-50/50 border border-amber-200/60 rounded-2xl space-y-1">
                            <span className="text-xs text-stone-500 uppercase tracking-wider font-semibold">Harga Produk</span>
                            <div className="text-2xl sm:text-3xl font-extrabold text-amber-950">
                                {formattedPrice}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <h3 className="font-bold text-stone-900 text-xs uppercase tracking-wider border-b border-stone-200 pb-2">Keterangan Singkat</h3>
                            <p className="text-stone-600 text-sm leading-relaxed">
                                {productShortDesc || 'Produk buatan pengrajin Jepara menggunakan kayu jati pilihan.'}
                            </p>
                        </div>

                        {!isLoggedIn ? (
                            <div className="pt-4 space-y-3">
                                <div className="p-5 rounded-2xl border border-stone-200 bg-stone-50">
                                    <div className="flex items-start gap-3">
                                        <div className="p-2 rounded-xl bg-amber-100 text-amber-900 shrink-0">
                                            <Lock className="h-5 w-5" />
                                        </div>
                                        <div className="space-y-3">
                                            <div>
                                                <p className="text-sm font-bold text-stone-900">
                                                    Masuk untuk Memesan ke Keranjang
                                                </p>
                                                <p className="text-xs text-stone-500 mt-1 leading-relaxed">
                                                    Silakan masuk ke akun Anda atau buat akun baru untuk menambahkan produk ke keranjang belanja.
                                                </p>
                                            </div>
                                            <div className="flex flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => openAuthModal('login')}
                                                    className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-900 text-white text-xs font-bold hover:bg-amber-800 transition"
                                                >
                                                    <LogIn className="h-3.5 w-3.5" />
                                                    <span>Masuk</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => openAuthModal('register')}
                                                    className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-stone-300 bg-white text-stone-800 text-xs font-bold hover:bg-stone-100 transition"
                                                >
                                                    <UserPlus className="h-3.5 w-3.5 text-stone-500" />
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
                                    className="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-white font-bold text-sm shadow-xs transition"
                                    style={{ backgroundColor: '#075E54' }}
                                >
                                    <MessageCircle className="w-4 h-4" />
                                    <span>Tanya / Pesan via WhatsApp</span>
                                </a>
                            </div>
                        ) : (
                            <form onSubmit={addToCartHandler} className="pt-4 space-y-4">
                                <div>
                                    <label className="text-xs font-bold text-stone-600 uppercase tracking-wider">
                                        Jumlah Pembelian
                                    </label>
                                    <div className="mt-2 inline-flex items-center border border-stone-300 rounded-xl overflow-hidden bg-white">
                                        <button
                                            type="button"
                                            onClick={() => setQty((v) => Math.max(1, v - 1))}
                                            className="px-3.5 py-2 text-stone-600 hover:bg-stone-100 transition disabled:opacity-50"
                                            disabled={qty <= 1}
                                        >
                                            <Minus className="w-4 h-4" />
                                        </button>
                                        <input
                                            type="number"
                                            min={1}
                                            max={100}
                                            value={qty}
                                            onChange={(e) => {
                                                const v = parseInt(e.target.value, 10);
                                                setQty(Number.isFinite(v) && v > 0 ? Math.min(100, v) : 1);
                                            }}
                                            className="w-16 px-2 py-2 text-center font-extrabold text-stone-900 border-x border-stone-300 focus:outline-none text-sm"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setQty((v) => Math.min(100, v + 1))}
                                            className="px-3.5 py-2 text-stone-600 hover:bg-stone-100 transition"
                                        >
                                            <Plus className="w-4 h-4" />
                                        </button>
                                    </div>
                                    <div className="mt-1 text-xs text-stone-500">
                                        Total: <span className="font-extrabold text-amber-950">{formatRupiah((typeof productPrice === 'number' ? productPrice : parseFloat(String(productPrice)) || 0) * qty)}</span>
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
                                                ? 'bg-stone-300 text-stone-500 cursor-not-allowed'
                                                : 'bg-amber-900 text-stone-100 hover:bg-amber-800'
                                        }`}
                                    >
                                        <ShoppingCart className="w-4 h-4" />
                                        <span>{isOutOfStock ? 'Stok Habis' : processing ? 'Menambahkan...' : 'Tambah ke Keranjang'}</span>
                                    </button>

                                    <a
                                        href={buildWhatsAppUrl()}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="grow inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-stone-100 font-bold text-sm shadow-xs transition"
                                        style={{ backgroundColor: '#075E54' }}
                                    >
                                        <MessageCircle className="w-4 h-4" />
                                        <span>Pesan via WhatsApp</span>
                                    </a>
                                </div>
                            </form>
                        )}
                    </ScrollReveal>
                </div>

                {productDescription && (
                    <ScrollReveal direction="up" className="mt-14 pt-10 border-t border-stone-200/80">
                        <h2 className="text-lg font-extrabold text-stone-900 mb-4 border-l-4 border-amber-900 pl-3">
                            Deskripsi Produk
                        </h2>
                        <div
                            className="prose prose-stone max-w-none text-stone-600 leading-relaxed text-sm space-y-3"
                            dangerouslySetInnerHTML={{ __html: productDescription }}
                        />
                    </ScrollReveal>
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
