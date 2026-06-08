import React from 'react';
import { Link } from '@inertiajs/react';

interface LinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationProps {
    links: LinkItem[];
}

export default function Pagination({ links }: PaginationProps) {
    if (links.length <= 3) {
        return null;
    }

    return (
        <nav className="flex justify-center items-center gap-1.5 mt-12" aria-label="Pagination">
            {links.map((link, key) => {
                if (link.url === null) {
                    return (
                        <span
                            key={key}
                            className="inline-flex items-center justify-center min-w-[40px] h-10 px-3 py-2 text-sm text-stone-400 bg-stone-100 rounded-xl cursor-not-allowed border border-stone-200/50"
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                }

                return (
                    <Link
                        key={key}
                        href={link.url}
                        className={`inline-flex items-center justify-center min-w-[40px] h-10 px-3 py-2 text-sm font-semibold rounded-xl border transition-all duration-200 ${
                            link.active
                                ? 'bg-amber-900 text-stone-100 border-amber-900 shadow-md shadow-amber-900/15'
                                : 'bg-white text-stone-600 border-stone-200 hover:bg-stone-50 hover:text-stone-900 hover:border-stone-300'
                        }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                );
            })}
        </nav>
    );
}
