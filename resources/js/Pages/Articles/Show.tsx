import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { Article } from '@/types/mebel';

interface ArticleShowProps {
    article: Article;
}

export default function Show({ article }: ArticleShowProps) {
    const imageUrl = article.image.startsWith('http') || article.image.startsWith('/') 
        ? article.image 
        : `/storage/${article.image}`;

    const formattedDate = new Date(article.published_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    return (
        <PublicLayout>
            <Head>
                <title>{article.title} | Agus Mebel Jepara</title>
                <meta name="description" content={article.excerpt} />
                <meta property="og:title" content={`${article.title} | Agus Mebel Jepara`} />
                <meta property="og:description" content={article.excerpt} />
                <meta property="og:image" content={imageUrl} />
                <meta property="og:type" content="article" />
            </Head>

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
                {/* Back to list */}
                <div className="mb-8">
                    <Link
                        href={route('articles.index')}
                        className="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-stone-550 hover:text-amber-900 transition"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Artikel & Info
                    </Link>
                </div>

                {/* Article Wrapper using Semantic HTML */}
                <article className="bg-white rounded-3xl border border-stone-200/50 shadow-sm overflow-hidden p-6 sm:p-10 lg:p-12 space-y-8">
                    
                    {/* Header */}
                    <header className="space-y-4 text-center">
                        <div className="flex justify-center items-center gap-2 text-xs text-stone-400 font-semibold uppercase tracking-wider">
                            <span>Kategori Tips</span>
                            <span>•</span>
                            <time dateTime={article.published_at}>{formattedDate}</time>
                        </div>
                        
                        <h1 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-950 tracking-tight leading-snug max-w-3xl mx-auto">
                            {article.title}
                        </h1>

                        <p className="text-stone-500 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto italic">
                            "{article.excerpt}"
                        </p>
                    </header>

                    {/* Featured Image */}
                    <div className="aspect-[21/10] bg-stone-100 rounded-2xl overflow-hidden shadow-inner border border-stone-200/20">
                        <img
                            src={imageUrl}
                            alt={article.title}
                            className="w-full h-full object-cover object-center"
                        />
                    </div>

                    {/* Article Content */}
                    <div 
                        className="prose prose-stone prose-amber max-w-none text-stone-700 leading-relaxed text-sm sm:text-base space-y-6 pt-4 border-t border-stone-100"
                        dangerouslySetInnerHTML={{ __html: article.content }}
                    />

                    {/* Footer */}
                    <footer className="mt-12 pt-6 border-t border-stone-150 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-stone-450">
                        <div>
                            <span>Penulis: Admin Agus Mebel</span>
                        </div>
                        <div>
                            <span>Bagikan artikel ini ke media sosial Anda.</span>
                        </div>
                    </footer>

                </article>

                {/* Bottom CTA */}
                <div className="mt-12 p-8 bg-stone-950 text-white rounded-3xl border border-stone-850 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div className="space-y-1.5 text-center md:text-left">
                        <h3 className="text-lg font-bold text-stone-100">Ingin Punya Furniture Jati Impian?</h3>
                        <p className="text-xs text-stone-400 max-w-md leading-relaxed">Konsultasikan gratis desain furniture impian Anda dengan pengrajin kami langsung dari Jepara.</p>
                    </div>
                    <a
                        href="https://wa.me/6281234567890"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="w-full md:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-3 rounded-xl text-stone-100 font-extrabold text-sm shadow-md"
                        style={{ backgroundColor: '#075E54' }}
                    >
                        Tanya Pengrajin WA
                    </a>
                </div>

            </div>
        </PublicLayout>
    );
}
