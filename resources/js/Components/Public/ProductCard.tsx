import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Product, StockStatus } from '@/types/mebel';
import { ShoppingCart, MessageCircle, Check } from 'lucide-react';
import { formatRupiah } from '@/lib/utils';
import { PageProps } from '@/types';

const DEFAULT_PRODUCT_IMG = 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800';

interface ProductCardProps {
    product: Product;
    whatsappNumber: string;
    whatsappTemplate: string;
}

export default function ProductCard({ product, whatsappNumber, whatsappTemplate }: ProductCardProps) {
    const { auth } = usePage<PageProps>().props as any;
    const user = auth?.user;

    const [isAdding, setIsAdding] = useState(false);
    const [isAdded, setIsAdded] = useState(false);

    const productName        = product?.name ?? 'Produk';
    const productSlug        = product?.slug ?? '';
    const productShortDesc   = product?.short_description ?? '';
    const productPrice       = product?.price ?? 0;
    const productStockStatus = product?.stock_status ?? 'preorder';
    const productImages      = Array.isArray(product?.images) ? product.images : [];

    const primaryImage = productImages.find(img => img && img.is_primary) || productImages[0];
    const imageUrl = primaryImage?.url
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/')
            ? primaryImage.url
            : `/storage/${primaryImage.url}`)
        : DEFAULT_PRODUCT_IMG;

    const formattedPrice = formatRupiah(productPrice);

    const buildWhatsAppUrl = () => {
        const text = whatsappTemplate
            ? whatsappTemplate
                .replaceAll('{product_name}', productName)
                .replaceAll('{product_price}', formattedPrice)
            : `Halo, saya tertarik dengan produk ${productName} seharga ${formattedPrice}. Apakah masih tersedia?`;
        return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`;
    };

    const handleAddToCart = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (!user) {
            router.visit(route('products.show', productSlug));
            return;
        }
        setIsAdding(true);
        router.post(
            route('cart.store'),
            { product_id: product.id, quantity: 1 },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsAdding(false);
                    setIsAdded(true);
                    setTimeout(() => setIsAdded(false), 2000);
                },
                onError: () => setIsAdding(false),
            }
        );
    };

    const status = productStockStatus as StockStatus;

    return (
        <div className="group bg-white rounded-2xl overflow-hidden border border-mahogany-100/60 shadow-xs hover:shadow-xl hover:border-mahogany-300/50 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
            <Link href={route('products.show', productSlug)} className="flex flex-col grow cursor-pointer">

                {/* Gambar */}
                <div className="relative aspect-square overflow-hidden bg-mahogany-50">
                    <img
                        src={imageUrl}
                        alt={productName}
                        loading="lazy"
                        className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                    />
                    {/* Vignette hover */}
                    <div className="absolute inset-0 bg-gradient-to-t from-mahogany-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                    {/* Badge status */}
                    <div className="absolute top-3 left-3">
                        {status === 'available' ? (
                            <span className="bg-emerald-800 text-white text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md shadow-xs">
                                Ready Stock
                            </span>
                        ) : status === 'out_of_stock' ? (
                            <span className="bg-rose-800 text-white text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md shadow-xs">
                                Stok Habis
                            </span>
                        ) : (
                            <span className="bg-mahogany-700 text-white text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md shadow-xs">
                                Pre Order
                            </span>
                        )}
                    </div>
                </div>

                {/* Info produk */}
                <div className="p-4 sm:p-5 flex flex-col grow">
                    <h3 className="text-base font-bold text-stone-900 line-clamp-1 group-hover:text-mahogany-800 transition-colors duration-200">
                        {productName}
                    </h3>

                    <p className="mt-1.5 text-xs text-stone-500 line-clamp-2 leading-relaxed grow">
                        {productShortDesc || 'Produk kayu jati pilihan dari pengrajin Jepara.'}
                    </p>

                    <div className="mt-4 pt-3.5 border-t border-mahogany-50 flex items-center justify-between gap-2">
                        <div>
                            <span className="text-[10px] text-mahogany-400 block uppercase tracking-wider font-semibold">Harga</span>
                            <span className="text-base font-extrabold text-mahogany-900 block">
                                {formattedPrice}
                            </span>
                        </div>

                        <div className="flex items-center gap-1.5 z-10" onClick={(e) => e.stopPropagation()}>
                            {/* Tombol keranjang */}
                            <button
                                type="button"
                                onClick={handleAddToCart}
                                disabled={isAdding || status === 'out_of_stock'}
                                className={`inline-flex items-center justify-center p-2.5 rounded-xl font-bold shadow-xs transition ${
                                    isAdded
                                        ? 'bg-emerald-700 text-white'
                                        : isAdding
                                        ? 'bg-mahogany-100 text-mahogany-700 opacity-70 cursor-wait'
                                        : status === 'out_of_stock'
                                        ? 'bg-stone-200 text-stone-400 cursor-not-allowed'
                                        : 'bg-mahogany-800 text-white hover:bg-mahogany-700 active:scale-95'
                                }`}
                                title="Tambah ke Keranjang"
                                aria-label="Tambah ke Keranjang"
                            >
                                {isAdded ? <Check className="w-4 h-4" /> : <ShoppingCart className="w-4 h-4" />}
                            </button>

                            {/* Tombol WhatsApp */}
                            <a
                                href={buildWhatsAppUrl()}
                                target="_blank"
                                rel="noopener noreferrer"
                                onClick={(e) => e.stopPropagation()}
                                className="inline-flex items-center justify-center p-2.5 rounded-xl text-white font-bold shadow-xs transition hover:opacity-90 active:scale-95"
                                style={{ backgroundColor: '#075E54' }}
                                title="Pesan via WhatsApp"
                                aria-label="Pesan via WhatsApp"
                            >
                                <MessageCircle className="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    );
}
