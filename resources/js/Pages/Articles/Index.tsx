import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import Pagination from '@/Components/Public/Pagination';
import { Article } from '@/types/mebel';

interface ArticlesIndexProps {
    articles: {
        data: Article[];
        links: any[];
    };
}

export default function Index({ articles }: ArticlesIndexProps) {
    return (
        <PublicLayout>
            <Head>
                <title>Artikel & Inspirasi Mebel Kayu Jati | Agus Mebel</title>
                <meta name="description" content="Kumpulan tips memilih furniture, inspirasi dekorasi rumah, dan panduan perawatan furniture kayu jati asli dari Jepara." />
            </Head>

            {/* Header Banner */}
            <div className="bg-stone-900 py-16 sm:py-20 text-center text-white relative overflow-hidden">
                <div className="absolute inset-0 bg-[radial-gradient(#3e2723_1px,transparent_1px)] [background-size:16px_16px] opacity-15"></div>
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
                    <span className="text-amber-500 font-extrabold uppercase tracking-widest text-[10px] bg-amber-950/40 px-3 py-1 rounded-md">Tips & Inspirasi</span>
                    <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">Artikel & Info</h1>
                    <p className="text-sm sm:text-base text-stone-300 max-w-2xl mx-auto leading-relaxed">Panduan lengkap, informasi material kayu jati, tips perawatan furniture, serta ide tata ruang interior untuk hunian Anda.</p>
                </div>
            </div>

            {/* Articles Grid */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                {articles.data && articles.data.length > 0 ? (
                    <>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {articles.data.map((article) => {
                                const imageUrl = article.image.startsWith('http') || article.image.startsWith('/') 
                                    ? article.image 
                                    : `/storage/${article.image}`;
                                return (
                                    <article 
                                        key={article.id}
                                        className="group bg-white rounded-2xl overflow-hidden border border-stone-200/60 shadow-sm hover:shadow-xl hover:border-amber-900/20 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full"
                                    >
                                        {/* Image Frame */}
                                        <Link href={route('articles.show', article.slug)} className="block aspect-[16/10] overflow-hidden bg-stone-150 relative">
                                            <img
                                                src={imageUrl}
                                                alt={article.title}
                                                loading="lazy"
                                                className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                                            />
                                            
                                            {article.is_hero && (
                                                <span className="absolute top-4 left-4 bg-emerald-800 text-stone-100 text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded shadow-sm">
                                                    Hero
                                                </span>
                                            )}
                                        </Link>

                                        {/* Content */}
                                        <div className="p-6 flex flex-col flex-grow">
                                            <div className="text-xs text-stone-400 font-semibold mb-2 flex items-center gap-1">
                                                <span>⏰</span>
                                                <span>
                                                    {new Date(article.published_at).toLocaleDateString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'long',
                                                        year: 'numeric'
                                                    })}
                                                </span>
                                            </div>

                                            <h3 className="text-base font-bold text-stone-900 line-clamp-2 leading-snug group-hover:text-amber-900 transition-colors duration-200">
                                                <Link href={route('articles.show', article.slug)}>
                                                    {article.title}
                                                </Link>
                                            </h3>

                                            <p className="mt-2.5 text-xs text-stone-500 leading-relaxed line-clamp-3 flex-grow">
                                                {article.excerpt}
                                            </p>

                                            <div className="mt-5 pt-4 border-t border-stone-100">
                                                <Link
                                                    href={route('articles.show', article.slug)}
                                                    className="text-xs font-bold text-amber-900 hover:text-amber-800 transition flex items-center gap-1 group/btn"
                                                >
                                                    Baca Selengkapnya
                                                    <svg className="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </Link>
                                            </div>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                        <Pagination links={articles.links} />
                    </>
                ) : (
                    <div className="bg-white p-16 text-center rounded-2xl border border-stone-200/60 shadow-sm max-w-md mx-auto space-y-4">
                        <span className="text-5xl block">📰</span>
                        <h3 className="text-lg font-bold text-stone-900">Belum Ada Artikel</h3>
                        <p className="text-stone-500 text-sm leading-relaxed">
                            Saat ini kami belum menerbitkan artikel atau info tips. Silakan kembali lagi di lain waktu.
                        </p>
                        <Link
                            href={route('home')}
                            className="inline-flex px-5 py-2.5 bg-amber-900 hover:bg-amber-800 text-stone-100 font-bold rounded-xl shadow-md transition text-xs"
                        >
                            Kembali ke Beranda
                        </Link>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
