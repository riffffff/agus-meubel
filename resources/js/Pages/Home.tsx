import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import HeroSection from '@/Components/Public/HeroSection';
import ProductCard from '@/Components/Public/ProductCard';
import { Article, Product, Review, ShopSetting } from '@/types/mebel';
import { ArrowRight, Hammer, Sofa, Truck } from 'lucide-react';

interface HomeProps {
    heroArticles: Article[];
    topProducts: Product[];
    reviews: Review[];
    shopSettings: ShopSetting;
}

export default function Home({ heroArticles, topProducts, reviews, shopSettings }: HomeProps) {
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const waTemplate = shopSettings?.whatsapp_template || '';

    const safeHeroArticles = Array.isArray(heroArticles) ? heroArticles.filter(Boolean) : [];
    const safeTopProducts = Array.isArray(topProducts) ? topProducts.filter(Boolean) : [];
    const safeReviews = Array.isArray(reviews) ? reviews.filter(Boolean) : [];

    return (
        <PublicLayout>
            <Head>
                <title>Agus Mebel Jepara | Pusat Furniture Jati Kualitas Premium</title>
                <meta name="description" content="Produsen & supplier furniture kayu jati asli Jepara. Menyediakan set meja makan, kursi tamu, lemari pakaian, dan custom design dengan garansi kualitas & pengiriman aman." />
                <meta property="og:title" content="Agus Mebel Jepara | Pusat Furniture Jati Kualitas Premium" />
                <meta property="og:description" content="Produsen & supplier furniture kayu jati asli Jepara. Menyediakan set meja makan, kursi tamu, lemari pakaian, dan custom design." />
            </Head>

            {safeHeroArticles.length > 0 ? (
                <HeroSection articles={safeHeroArticles} />
            ) : (
                <div className="relative bg-stone-900 overflow-hidden py-24 sm:py-32">
                    <div className="absolute inset-0">
                        <img
                            src="https://images.unsplash.com/photo-1540518614846-7eded433c457?auto=format&fit=crop&q=80&w=1800"
                            className="w-full h-full object-cover opacity-25"
                            alt="Agus Mebel Jepara Banner"
                        />
                        <div className="absolute inset-0 bg-linear-to-t from-stone-950 via-stone-900/60 to-transparent"></div>
                    </div>
                    <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white space-y-6">
                        <span className="bg-amber-900/80 text-stone-100 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-md">Original Jepara Craftsmanship</span>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight">Furniture Kayu Jati Premium</h1>
                        <p className="text-lg text-stone-300 max-w-3xl mx-auto leading-relaxed">Dibuat langsung oleh pengrajin profesional Jepara menggunakan bahan kayu jati solid pilihan. Awet, estetik, dan bernilai seni tinggi.</p>
                        <div className="flex justify-center gap-4 pt-4">
                            <Link href={route('products.index')} className="px-6 py-3 bg-amber-900 hover:bg-amber-800 text-stone-100 font-bold rounded-xl shadow-lg transition">Jelajahi Produk</Link>
                            <a href={`https://wa.me/${waNumber}`} target="_blank" rel="noopener noreferrer" className="px-6 py-3 bg-stone-100 hover:bg-stone-200 text-stone-900 font-bold rounded-xl shadow-lg transition">Konsultasi Custom</a>
                        </div>
                    </div>
                </div>
            )}

            <section className="py-16 bg-stone-100 border-y border-stone-200/50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div className="bg-white p-8 rounded-2xl border border-stone-200/30 shadow-sm flex gap-4 items-start">
                            <div className="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-900 flex items-center justify-center shrink-0" style={{ backgroundColor: 'rgba(120,53,4,0.1)' }}>
                                <Sofa className="h-6 w-6" />
                            </div>
                            <div className="space-y-2">
                                <h3 className="text-lg font-bold text-stone-900">100% Kayu Jati Pilihan</h3>
                                <p className="text-stone-500 text-sm leading-relaxed">Menggunakan material kayu jati berkualitas standar perhutani (TPK) yang terkenal awet, kokoh, dan tahan lama.</p>
                            </div>
                        </div>
                        <div className="bg-white p-8 rounded-2xl border border-stone-200/30 shadow-sm flex gap-4 items-start">
                            <div className="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-900 flex items-center justify-center shrink-0" style={{ backgroundColor: 'rgba(120,53,4,0.1)' }}>
                                <Hammer className="h-6 w-6" />
                            </div>
                            <div className="space-y-2">
                                <h3 className="text-lg font-bold text-stone-900">Custom Desain Bebas</h3>
                                <p className="text-stone-500 text-sm leading-relaxed">Sesuaikan ukuran, warna finishing, serta model furniture sesuai dengan impian ruangan rumah Anda.</p>
                            </div>
                        </div>
                        <div className="bg-white p-8 rounded-2xl border border-stone-200/30 shadow-sm flex gap-4 items-start">
                            <div className="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-900 flex items-center justify-center shrink-0" style={{ backgroundColor: 'rgba(120,53,4,0.1)' }}>
                                <Truck className="h-6 w-6" />
                            </div>
                            <div className="space-y-2">
                                <h3 className="text-lg font-bold text-stone-900">Garansi Pengiriman Aman</h3>
                                <p className="text-stone-500 text-sm leading-relaxed">Pengiriman ke seluruh Indonesia menggunakan armada kargo khusus furniture terpercaya dengan proteksi penuh.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
                <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12">
                    <div className="space-y-2">
                        <span className="text-emerald-800 font-bold uppercase tracking-wider text-xs block">Katalog Terbaik</span>
                        <h2 className="text-3xl font-extrabold text-stone-950">Produk Unggulan Kami</h2>
                        <p className="text-stone-500 text-sm">Temukan berbagai furniture jati berkualitas tinggi pilihan pelanggan untuk dekorasi hunian impian Anda.</p>
                    </div>
                    <div>
                        <Link
                            href={route('products.index')}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-amber-900 text-amber-900 font-bold hover:bg-amber-900 hover:text-stone-100 transition duration-300 shadow-sm"
                        >
                            Lihat Semua Produk
                            <ArrowRight className="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                {safeTopProducts.length > 0 ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        {safeTopProducts.map((product) => (
                            <ProductCard
                                key={product?.id ?? Math.random()}
                                product={product}
                                whatsappNumber={waNumber}
                                whatsappTemplate={waTemplate}
                            />
                        ))}
                    </div>
                ) : (
                    <div className="bg-white p-12 text-center rounded-2xl border border-stone-200/80 shadow-sm space-y-4 max-w-md mx-auto">
                        <Sofa className="mx-auto h-10 w-10 text-amber-900" />
                        <h3 className="text-lg font-bold text-stone-900">Katalog Produk Masih Kosong</h3>
                        <p className="text-stone-500 text-sm leading-relaxed">Saat ini kami belum mengunggah katalog produk. Silakan kembali lagi nanti atau hubungi kami langsung via WhatsApp.</p>
                    </div>
                )}
            </section>

            {safeReviews.length > 0 && (
                <section className="py-20 bg-stone-100 border-t border-stone-200/50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center max-w-3xl mx-auto space-y-3 mb-16">
                            <span className="text-emerald-800 font-bold uppercase tracking-wider text-xs block">Testimoni Pembeli</span>
                            <h2 className="text-3xl font-extrabold text-stone-950">Apa Kata Mereka?</h2>
                            <p className="text-stone-500 text-sm leading-relaxed">Kepuasan pelanggan adalah prioritas utama kami. Berikut ulasan tulus dari para pelanggan setia Agus Mebel Jepara.</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {safeReviews.map((rev) => {
                                const revName = rev?.name ?? 'Pelanggan';
                                const revCity = rev?.city ?? '';
                                const revRating = typeof rev?.rating === 'number' && rev.rating >= 1 && rev.rating <= 5 ? rev.rating : 5;
                                const revReview = rev?.review ?? '';
                                const revId = rev?.id ?? Math.random();

                                return (
                                    <div key={revId} className="bg-white p-8 rounded-2xl border border-stone-200/30 shadow-sm flex flex-col justify-between">
                                        <div className="space-y-4">
                                            <div className="flex gap-1 text-amber-500">
                                                {Array.from({ length: 5 }).map((_, i) => (
                                                    <svg
                                                        key={i}
                                                        className={`w-4 h-4 fill-current ${i < revRating ? 'text-amber-500' : 'text-stone-200'}`}
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                ))}
                                            </div>

                                            {revReview && (
                                                <p className="text-stone-600 text-sm leading-relaxed italic">
                                                    "{revReview}"
                                                </p>
                                            )}
                                        </div>

                                        <div className="mt-6 pt-4 border-t border-stone-100 flex items-center gap-3">
                                            <div className="w-10 h-10 rounded-full bg-stone-100 flex items-center justify-center font-bold text-amber-900 border border-stone-200/50">
                                                {revName.charAt(0)}
                                            </div>
                                            <div>
                                                <h4 className="text-sm font-bold text-stone-900">{revName}</h4>
                                                {revCity && <span className="text-xs text-stone-400">{revCity}</span>}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
