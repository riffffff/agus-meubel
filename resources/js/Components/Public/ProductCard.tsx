import React from 'react';
import { Link } from '@inertiajs/react';
import { Product, StockStatus } from '@/types/mebel';
import { Eye, MessageCircle } from 'lucide-react';
import { formatRupiah } from '@/lib/utils';

const DEFAULT_PRODUCT_IMG = 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800';

interface ProductCardProps {
    product: Product;
    whatsappNumber: string;
    whatsappTemplate: string;
}

export default function ProductCard({ product, whatsappNumber, whatsappTemplate }: ProductCardProps) {
    const productName = product?.name ?? 'Produk';
    const productSlug = product?.slug ?? '';
    const productShortDesc = product?.short_description ?? '';
    const productPrice = product?.price ?? 0;
    const productStockStatus = product?.stock_status ?? 'preorder';
    const productImages = Array.isArray(product?.images) ? product.images : [];

    const primaryImage = productImages.find(img => img && img.is_primary) || productImages[0];
    const imageUrl = primaryImage?.url
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/')
            ? primaryImage.url
            : `/storage/${primaryImage.url}`)
        : DEFAULT_PRODUCT_IMG;

    const formattedPrice = formatRupiah(productPrice);

    const buildWhatsAppUrl = () => {
        let text = whatsappTemplate
            ? whatsappTemplate
                .replaceAll('{product_name}', productName)
                .replaceAll('{product_price}', formattedPrice)
            : `Halo, saya tertarik dengan produk ${productName} seharga ${formattedPrice}. Apakah masih tersedia?`;

        return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`;
    };

    const status = productStockStatus as StockStatus;

    return (
        <div className="group bg-white rounded-2xl overflow-hidden border border-stone-200/70 shadow-xs hover:shadow-lg hover:border-amber-900/30 hover:-translate-y-0.5 transition-all duration-300 flex flex-col h-full">
            <div className="relative aspect-square overflow-hidden bg-stone-100">
                <img
                    src={imageUrl}
                    alt={productName}
                    loading="lazy"
                    className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                />

                <div className="absolute top-3 left-3">
                    {status === 'available' ? (
                        <span className="bg-emerald-800 text-stone-100 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md shadow-xs">
                            Ready Stock
                        </span>
                    ) : status === 'out_of_stock' ? (
                        <span className="bg-rose-800 text-stone-100 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md shadow-xs">
                            Stok Habis
                        </span>
                    ) : (
                        <span className="bg-amber-800 text-stone-100 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md shadow-xs">
                            Pre Order
                        </span>
                    )}
                </div>
            </div>

            <div className="p-4 sm:p-5 flex flex-col grow">
                <h3 className="text-base font-bold text-stone-900 line-clamp-1 group-hover:text-amber-900 transition-colors duration-200">
                    {productName}
                </h3>

                <p className="mt-1.5 text-xs text-stone-500 line-clamp-2 leading-relaxed grow">
                    {productShortDesc || 'Produk kayu jati pilihan dari pengrajin Jepara.'}
                </p>

                <div className="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between gap-2">
                    <div>
                        <span className="text-[10px] text-stone-400 block uppercase tracking-wider font-semibold">Harga</span>
                        <span className="text-base font-extrabold text-amber-950 block">
                            {formattedPrice}
                        </span>
                    </div>

                    <div className="flex gap-1.5">
                        <Link
                            href={route('products.show', productSlug)}
                            className="inline-flex items-center justify-center p-2 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition"
                            title="Detail Produk"
                        >
                            <Eye className="w-4 h-4" />
                        </Link>

                        <a
                            href={buildWhatsAppUrl()}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-stone-100 font-bold text-xs shadow-xs transition"
                            style={{ backgroundColor: '#075E54' }}
                        >
                            <MessageCircle className="w-4 h-4" />
                            Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
}
