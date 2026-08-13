import React from 'react';
import { Link } from '@inertiajs/react';
import { Article } from '@/types/mebel';
import { ArrowRight } from 'lucide-react';

const DEFAULT_HERO_IMG = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=1600';

interface HeroSectionProps {
    articles: Article[];
}

export default function HeroSection({ articles }: HeroSectionProps) {
    const validArticles = Array.isArray(articles) ? articles.filter(Boolean) : [];
    if (validArticles.length === 0) {
        return null;
    }

    const renderHeroCard = (article: Article, isLarge: boolean = false) => {
        if (!article) return null;

        const artImage = article?.image ?? '';
        const imageUrl = artImage && (artImage.startsWith('http') || artImage.startsWith('/'))
            ? artImage
            : artImage
                ? `/storage/${artImage}`
                : DEFAULT_HERO_IMG;

        const artTitle = article?.title ?? '';
        const artSlug = article?.slug ?? '';
        const artExcerpt = article?.excerpt ?? '';
        const artPublishedAt = article?.published_at ?? '';
        const artId = article?.id ?? Math.random();

        const formattedDate = artPublishedAt
            ? new Date(artPublishedAt).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            })
            : '';

        return (
            <Link
                href={route('articles.show', artSlug)}
                key={artId}
                className="relative block group overflow-hidden w-full h-full rounded-2xl shadow-md border border-stone-200/50 hover:shadow-xl hover:border-amber-900/35 transition-all duration-300"
            >
                <div className="absolute inset-0 bg-stone-900">
                    <img
                        src={imageUrl}
                        alt={artTitle}
                        loading="eager"
                        className="w-full h-full object-cover object-center opacity-85 group-hover:scale-105 transition-transform duration-700 ease-out"
                    />
                </div>

                <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent group-hover:via-black/50 transition duration-300"></div>

                <div className="absolute inset-x-0 bottom-0 p-6 sm:p-8 flex flex-col justify-end h-full text-white">
                    <div className="space-y-3">
                        <div className="flex gap-2">
                            <span className="bg-emerald-800/95 text-stone-100 text-[10px] uppercase font-bold tracking-widest px-2.5 py-1 rounded-md">
                                Artikel Pilihan
                            </span>
                            {formattedDate && (
                                <span className="text-stone-300/90 text-xs mt-0.5">
                                    {formattedDate}
                                </span>
                            )}
                        </div>

                        <h2 className={`font-bold tracking-tight leading-snug group-hover:text-amber-300 transition duration-300 ${
                            isLarge ? 'text-2xl sm:text-3xl lg:text-4xl' : 'text-lg sm:text-xl'
                        }`}>
                            {artTitle}
                        </h2>

                        {artExcerpt && (
                            <p className={`text-stone-300 line-clamp-2 leading-relaxed ${
                                isLarge ? 'text-sm sm:text-base max-w-2xl' : 'text-xs sm:text-sm'
                            }`}>
                                {artExcerpt}
                            </p>
                        )}

                        <div className="pt-2 flex items-center gap-1.5 text-xs font-bold text-amber-400 group-hover:translate-x-1 transition-transform">
                            Baca Selengkapnya
                            <ArrowRight className="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </Link>
        );
    };

    const count = validArticles.length;

    return (
        <section className="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
            {count === 1 && (
                <div className="h-[450px] sm:h-[550px] w-full">
                    {renderHeroCard(validArticles[0], true)}
                </div>
            )}

            {count === 2 && (
                <div className="grid grid-cols-1 md:grid-cols-5 gap-6 h-auto md:h-[500px]">
                    <div className="md:col-span-3 h-[380px] md:h-full">
                        {renderHeroCard(validArticles[0], true)}
                    </div>
                    <div className="md:col-span-2 h-[280px] md:h-full">
                        {renderHeroCard(validArticles[1], false)}
                    </div>
                </div>
            )}

            {count >= 3 && (
                <div className="grid grid-cols-1 lg:grid-cols-5 gap-6 h-auto lg:h-[550px]">
                    <div className="lg:col-span-3 h-[380px] lg:h-full">
                        {renderHeroCard(validArticles[0], true)}
                    </div>
                    <div className="lg:col-span-2 flex flex-col gap-6 h-[500px] lg:h-full">
                        <div className="flex-1 min-h-[235px]">
                            {renderHeroCard(validArticles[1], false)}
                        </div>
                        <div className="flex-1 min-h-[235px]">
                            {renderHeroCard(validArticles[2], false)}
                        </div>
                    </div>
                </div>
            )}
        </section>
    );
}
