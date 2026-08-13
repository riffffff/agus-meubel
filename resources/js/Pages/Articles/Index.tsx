import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import Pagination from '@/Components/Public/Pagination';
import { Article } from '@/types/mebel';
import { ArrowRight, Clock3, Newspaper } from 'lucide-react';

const DEFAULT_ARTICLE_IMG = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=800';

interface ArticlesIndexProps {
    articles: {
        data: Article[];
        links: any[];
    };
}

export default function Index({ articles }: ArticlesIndexProps) {
    const articlesData = Array.isArray(articles?.data) ? articles.data : [];
    const articlesLinks = Array.isArray(articles?.links) ? articles.links : [];

    return (
        <PublicLayout>
            <Head>
                <title>Artikel & Inspirasi Mebel Kayu Jati | Agus Mebel</title>
                <meta name="description" content="Kumpulan tips memilih furniture, inspirasi dekorasi rumah, dan panduan perawatan furniture kayu jati asli dari Jepara." />
            </Head>

            <div className="bg-stone-900 py-16 sm:py-20 text-center text-white relative overflow-hidden">
                <div className="absolute inset-0 bg-[radial-gradient(#3e2723_1px,transparent_1px)] bg-size-[16px_16px] opacity-15"></div>
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
                    <span className="text-amber-500 font-extrabold uppercase tracking-widest text-[10px] bg-amber-950/40 px-3 py-1 rounded-md">Tips & Inspirasi</span>
                    <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">Artikel & Info</h1>
                    <p className="text-sm sm:text-base text-stone-300 max-w-2xl mx-auto leading-relaxed">Panduan lengkap, informasi material kayu jati, tips perawatan furniture, serta ide tata ruang interior untuk hunian Anda.</p>
                </div>
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                {articlesData.length > 0 ? (
                    <>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {articlesData.map((article) => {
                                if (!article) return null;
                                const artImage = article?.image ?? '';
                                const imageUrl = artImage && (artImage.startsWith('http') || artImage.startsWith('/'))
                                    ? artImage
                                    : artImage
                                        ? `/storage/${artImage}`
                                        : DEFAULT_ARTICLE_IMG;

                                const artTitle = article?.title ?? '';
                                const artSlug = article?.slug ?? '';
                                const artExcerpt = article?.excerpt ?? '';
                                const artPublishedAt = article?.published_at ?? '';
                                const artIsHero = article?.is_hero ?? false;
                                const artId = article?.id ?? Math.random();

                                const formattedDate = artPublishedAt
                                    ? new Date(artPublishedAt).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    })
                                    : '';

                                return (
                                    <article
                                        key={artId}
                                        className="group bg-white rounded-2xl overflow-hidden border border-stone-200/60 shadow-sm hover:shadow-xl hover:border-amber-900/20 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full"
                                    >
                                        <Link href={route('articles.show', artSlug)} className="block aspect-16/10 overflow-hidden bg-stone-100 relative">
                                            <img
                                                src={imageUrl}
                                                alt={artTitle}
                                                loading="lazy"
                                                className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                                            />

                                            {artIsHero && (
                                                <span className="absolute top-4 left-4 bg-emerald-800 text-stone-100 text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded shadow-sm">
                                                    Hero
                                                </span>
                                            )}
                                        </Link>

                                        <div className="p-6 flex flex-col grow">
                                            {formattedDate && (
                                                <div className="text-xs text-stone-400 font-semibold mb-2 flex items-center gap-1">
                                                    <Clock3 className="h-3.5 w-3.5" />
                                                    <span>{formattedDate}</span>
                                                </div>
                                            )}

                                            <h3 className="text-base font-bold text-stone-900 line-clamp-2 leading-snug group-hover:text-amber-900 transition-colors duration-200">
                                                <Link href={route('articles.show', artSlug)}>
                                                    {artTitle}
                                                </Link>
                                            </h3>

                                            <p className="mt-2.5 text-xs text-stone-500 leading-relaxed line-clamp-3 grow">
                                                {artExcerpt}
                                            </p>

                                            <div className="mt-5 pt-4 border-t border-stone-100">
                                                <Link
                                                    href={route('articles.show', artSlug)}
                                                    className="text-xs font-bold text-amber-900 hover:text-amber-800 transition flex items-center gap-1 group/btn"
                                                >
                                                    Baca Selengkapnya
                                                    <ArrowRight className="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" />
                                                </Link>
                                            </div>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                        <Pagination links={articlesLinks} />
                    </>
                ) : (
                    <div className="bg-white p-16 text-center rounded-2xl border border-stone-200/60 shadow-sm max-w-md mx-auto space-y-4">
                        <Newspaper className="mx-auto h-12 w-12 text-amber-900" />
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
