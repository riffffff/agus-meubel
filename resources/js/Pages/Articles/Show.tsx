import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Article } from '@/types/mebel';
import { ArrowLeft, MessageCircle } from 'lucide-react';
import { PageProps } from '@/types';

interface ArticleShowProps {
    article: Article;
}

const DEFAULT_ARTICLE_IMAGE = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=1600';

export default function Show({ article }: ArticleShowProps) {
    const { shopSettings } = usePage<PageProps>().props;
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';

    const articleTitle = article?.title ?? 'Artikel';
    const articleExcerpt = article?.excerpt ?? '';
    const articleImage = article?.image ?? '';
    const articleContent = article?.content ?? '';
    const articlePublishedAt = article?.published_at ?? new Date().toISOString();

    const imageUrl = articleImage && (articleImage.startsWith('http') || articleImage.startsWith('/'))
        ? articleImage
        : articleImage
            ? `/storage/${articleImage}`
            : DEFAULT_ARTICLE_IMAGE;

    const formattedDate = articlePublishedAt
        ? new Date(articlePublishedAt).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        })
        : '';

    const pageTitle = `${articleTitle} | Agus Mebel Jepara`;
    const metaDescription = articleExcerpt || 'Artikel inspirasi dan tips seputar furniture kayu jati premium dari Agus Mebel Jepara.';

    return (
        <PublicLayout>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={metaDescription} />
                <meta property="og:title" content={pageTitle} />
                <meta property="og:description" content={metaDescription} />
                <meta property="og:image" content={imageUrl} />
                <meta property="og:type" content="article" />
            </Head>

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
                <div className="mb-8">
                    <Link
                        href={route('articles.index')}
                        className="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-stone-600 hover:text-amber-900 transition"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Kembali ke Artikel & Info
                    </Link>
                </div>

                <article className="bg-white rounded-3xl border border-stone-200/50 shadow-sm overflow-hidden p-6 sm:p-10 lg:p-12 space-y-8">

                    <header className="space-y-4 text-center">
                        <div className="flex justify-center items-center gap-2 text-xs text-stone-400 font-semibold uppercase tracking-wider">
                            <span>Kategori Tips</span>
                            {formattedDate && (
                                <>
                                    <span>•</span>
                                    <time dateTime={articlePublishedAt}>{formattedDate}</time>
                                </>
                            )}
                        </div>

                        <h1 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-950 tracking-tight leading-snug max-w-3xl mx-auto">
                            {articleTitle}
                        </h1>

                        {articleExcerpt && (
                            <p className="text-stone-500 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto italic">
                                "{articleExcerpt}"
                            </p>
                        )}
                    </header>

                    {imageUrl && (
                        <div className="aspect-21/10 bg-stone-100 rounded-2xl overflow-hidden shadow-inner border border-stone-200/20">
                            <img
                                src={imageUrl}
                                alt={articleTitle}
                                className="w-full h-full object-cover object-center"
                            />
                        </div>
                    )}

                    {articleContent && (
                        <div
                            className="prose prose-stone prose-amber max-w-none text-stone-700 leading-relaxed text-sm sm:text-base space-y-6 pt-4 border-t border-stone-100"
                            dangerouslySetInnerHTML={{ __html: articleContent }}
                        />
                    )}

                    <footer className="mt-12 pt-6 border-t border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-stone-500">
                        <div>
                            <span>Penulis: Admin Agus Mebel</span>
                        </div>
                        <div>
                            <span>Bagikan artikel ini ke media sosial Anda.</span>
                        </div>
                    </footer>

                </article>

                <div className="mt-12 p-8 bg-stone-950 text-white rounded-3xl border border-stone-900 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div className="space-y-1.5 text-center md:text-left">
                        <h3 className="text-lg font-bold text-stone-100">Ingin Punya Furniture Jati Impian?</h3>
                        <p className="text-xs text-stone-400 max-w-md leading-relaxed">Konsultasikan gratis desain furniture impian Anda dengan pengrajin kami langsung dari Jepara.</p>
                    </div>
                    <a
                        href={`https://wa.me/${waNumber}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="w-full md:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-3 rounded-xl text-stone-100 font-extrabold text-sm shadow-md"
                        style={{ backgroundColor: '#075E54' }}
                    >
                        <MessageCircle className="h-4 w-4" />
                        Tanya Pengrajin WA
                    </a>
                </div>

            </div>
        </PublicLayout>
    );
}
