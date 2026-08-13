import React, { useState, useEffect, useRef } from 'react';
import { Link } from '@inertiajs/react';
import { Article } from '@/types/mebel';
import { ArrowRight, ChevronLeft, ChevronRight, Sparkles, Pause, Play, Eye } from 'lucide-react';

const DEFAULT_HERO_IMG = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=1600';

interface HeroSectionProps {
    articles: Article[];
}

export default function HeroSection({ articles }: HeroSectionProps) {
    const validArticles = Array.isArray(articles) ? articles.filter(Boolean) : [];
    const [currentIndex, setCurrentIndex] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const timerRef = useRef<NodeJS.Timeout | null>(null);

    const count = validArticles.length;

    useEffect(() => {
        if (count <= 1 || isPaused) return;

        timerRef.current = setInterval(() => {
            setCurrentIndex((prevIndex) => (prevIndex + 1) % count);
        }, 6000);

        return () => {
            if (timerRef.current) clearInterval(timerRef.current);
        };
    }, [count, isPaused, currentIndex]);

    if (count === 0) return null;

    const currentArticle = validArticles[currentIndex];
    const artTitle = currentArticle?.title ?? 'Furniture Kayu Jati Premium';
    const artSlug = currentArticle?.slug ?? '';
    const artExcerpt = currentArticle?.excerpt ?? '';
    const artPublishedAt = currentArticle?.published_at ?? '';

    const formattedDate = artPublishedAt
        ? new Date(artPublishedAt).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        })
        : '';

    const prevSlide = () => {
        setCurrentIndex((prevIndex) => (prevIndex - 1 + count) % count);
    };

    const nextSlide = () => {
        setCurrentIndex((prevIndex) => (prevIndex + 1) % count);
    };

    return (
        <section className="py-6 px-4 sm:px-6 lg:px-8 max-w-[1440px] mx-auto w-full">
            <div
                className="relative overflow-hidden rounded-3xl bg-stone-900 shadow-2xl border border-stone-200/60 p-6 sm:p-8 lg:p-10"
                onMouseEnter={() => setIsPaused(true)}
                onMouseLeave={() => setIsPaused(false)}
            >
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    {/* Kolom Teks Informasi (Tidak Menutupi Gambar) */}
                    <div className="lg:col-span-5 flex flex-col justify-between space-y-6 z-20 text-white order-2 lg:order-1">
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-900/80 border border-amber-500/30 text-amber-200 text-xs font-bold uppercase tracking-widest">
                                    <Sparkles className="h-3.5 w-3.5 text-amber-400" />
                                    <span>Artikel Unggulan</span>
                                </div>
                                {count > 1 && (
                                    <button
                                        type="button"
                                        onClick={() => setIsPaused(!isPaused)}
                                        className="p-1.5 rounded-lg bg-stone-800 text-stone-400 hover:text-white transition text-xs"
                                        title={isPaused ? 'Putar Slider' : 'Jeda Slider'}
                                    >
                                        {isPaused ? <Play className="h-3.5 w-3.5 text-amber-400" /> : <Pause className="h-3.5 w-3.5" />}
                                    </button>
                                )}
                            </div>

                            {formattedDate && (
                                <span className="text-stone-400 text-xs font-semibold block">
                                    {formattedDate}
                                </span>
                            )}

                            <h2 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-stone-100 leading-snug">
                                {artTitle}
                            </h2>

                            {artExcerpt && (
                                <p className="text-stone-300 text-xs sm:text-sm leading-relaxed font-normal line-clamp-3">
                                    {artExcerpt}
                                </p>
                            )}
                        </div>

                        <div className="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            {artSlug && (
                                <Link
                                    href={route('articles.show', artSlug)}
                                    className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-900 hover:bg-amber-800 text-white font-bold text-xs shadow-md transition transform hover:-translate-y-0.5"
                                >
                                    <span>Baca Artikel</span>
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            )}
                            <Link
                                href={route('products.index')}
                                className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-200 font-bold text-xs border border-stone-700 transition"
                            >
                                <Eye className="h-4 w-4 text-amber-400" />
                                <span>Lihat Produk</span>
                            </Link>
                        </div>

                        {/* Slider Controller Bar */}
                        {count > 1 && (
                            <div className="pt-4 border-t border-stone-800 flex items-center justify-between">
                                <div className="flex items-center gap-1.5">
                                    {validArticles.map((_, idx) => (
                                        <button
                                            key={idx}
                                            onClick={() => setCurrentIndex(idx)}
                                            className={`h-2 rounded-full transition-all duration-300 ${
                                                idx === currentIndex
                                                    ? 'w-6 bg-amber-500'
                                                    : 'w-2 bg-stone-700 hover:bg-stone-500'
                                            }`}
                                        />
                                    ))}
                                </div>
                                <div className="flex items-center gap-2 text-stone-400 text-xs">
                                    <span>{currentIndex + 1} / {count}</span>
                                    <div className="flex gap-1 ml-2">
                                        <button
                                            type="button"
                                            onClick={prevSlide}
                                            className="p-2 rounded-lg bg-stone-800 hover:bg-amber-900 hover:text-white transition"
                                        >
                                            <ChevronLeft className="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={nextSlide}
                                            className="p-2 rounded-lg bg-stone-800 hover:bg-amber-900 hover:text-white transition"
                                        >
                                            <ChevronRight className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Kolom Tampilan Gambar (Bersih, Tidak Tertutup Teks) */}
                    <div className="lg:col-span-7 relative h-[280px] sm:h-[380px] lg:h-[420px] rounded-2xl overflow-hidden border border-stone-800 shadow-inner order-1 lg:order-2">
                        {validArticles.map((art, idx) => {
                            const img = art?.image;
                            const bgUrl = img && (img.startsWith('http') || img.startsWith('/'))
                                ? img
                                : img
                                    ? `/storage/${img}`
                                    : DEFAULT_HERO_IMG;
                            const isActive = idx === currentIndex;

                            return (
                                <div
                                    key={art.id ?? idx}
                                    className={`absolute inset-0 transition-opacity duration-700 ease-in-out ${
                                        isActive ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'
                                    }`}
                                >
                                    <img
                                        src={bgUrl}
                                        alt={art.title}
                                        className={`w-full h-full object-cover object-center transition-transform duration-5000 ease-out ${
                                            isActive ? 'scale-105' : 'scale-100'
                                        }`}
                                    />
                                    {/* Vignette tipis tanpa menutupi keindahan gambar */}
                                    <div className="absolute inset-0 bg-gradient-to-t from-stone-950/40 via-transparent to-transparent" />
                                </div>
                            );
                        })}
                    </div>

                </div>
            </div>
        </section>
    );
}
