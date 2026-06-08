import React from 'react';
import { Link } from '@inertiajs/react';
import { Product } from '@/types/mebel';

interface ProductCardProps {
    product: Product;
    whatsappNumber: string;
    whatsappTemplate: string;
}

export default function ProductCard({ product, whatsappNumber, whatsappTemplate }: ProductCardProps) {
    // Format Price to Rupiah
    const formatRupiah = (number: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(number);
    };

    // Find primary image or use fallback
    const primaryImage = product.images?.find(img => img.is_primary) || product.images?.[0];
    const imageUrl = primaryImage
        ? (primaryImage.url.startsWith('http') || primaryImage.url.startsWith('/') ? primaryImage.url : `/storage/${primaryImage.url}`)
        : 'https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=800'; // high quality placeholder

    // Build WhatsApp Link
    const formattedPrice = formatRupiah(product.price);
    const buildWhatsAppUrl = () => {
        let text = whatsappTemplate
            ? whatsappTemplate
                .replace('{product_name}', product.name)
                .replace('{product_price}', formattedPrice)
            : `Halo, saya tertarik dengan produk ${product.name} seharga ${formattedPrice}. Apakah masih tersedia?`;

        return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`;
    };

    return (
        <div className="group bg-white rounded-2xl overflow-hidden border border-stone-200/60 shadow-sm hover:shadow-xl hover:border-amber-900/20 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
            {/* Image Container */}
            <div className="relative aspect-square overflow-hidden bg-stone-100">
                <img
                    src={imageUrl}
                    alt={product.name}
                    loading="lazy"
                    className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                />
                
                {/* Stock Status Badge */}
                <div className="absolute top-4 left-4">
                    {product.stock_status === 'ready_stock' ? (
                        <span className="bg-emerald-800 text-stone-100 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-lg shadow-sm">
                            Ready Stock
                        </span>
                    ) : (
                        <span className="bg-amber-700 text-stone-100 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-lg shadow-sm">
                            Pre Order
                        </span>
                    )}
                </div>
            </div>

            {/* Content Container */}
            <div className="p-5 flex flex-col flex-grow">
                <h3 className="text-base font-bold text-stone-900 line-clamp-1 group-hover:text-amber-900 transition-colors duration-200">
                    {product.name}
                </h3>
                
                <p className="mt-1.5 text-xs text-stone-500 line-clamp-2 leading-relaxed flex-grow">
                    {product.short_description}
                </p>

                <div className="mt-4 pt-4 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span className="text-[10px] text-stone-400 block uppercase tracking-wider font-semibold">Harga Mulai</span>
                        <span className="text-base font-extrabold text-amber-950 block sm:inline">
                            {formattedPrice}
                        </span>
                    </div>
                    
                    <div className="flex gap-2">
                        <Link
                            href={route('products.show', product.slug)}
                            className="inline-flex items-center justify-center p-2.5 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition duration-200"
                            title="Lihat Detail"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </Link>
                        
                        <a
                            href={buildWhatsAppUrl()}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-stone-100 font-extrabold text-xs shadow-md shadow-emerald-900/10 hover:shadow-lg transition duration-300"
                            style={{ backgroundColor: '#075E54' }}
                        >
                            <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.731-1.456L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.625 1.45 5.436.002 9.852-4.41 9.855-9.852.002-2.637-1.023-5.114-2.883-6.976C16.381 1.916 13.91 .89 11.272.89c-5.433 0-9.85 4.417-9.853 9.856 0 1.562.488 3.09 1.41 4.481L1.8 20.353l5.05-1.325c-.244-.132-.244-.132.207.126z"/>
                            </svg>
                            Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
}
