import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Clock3,
    LogIn,
    MapPin,
    MessageCircle,
    Menu,
    Phone,
    Sofa,
    X,
} from 'lucide-react';
import { PageProps } from '@/types';

interface PublicLayoutProps {
    children: React.ReactNode;
}

export default function PublicLayout({ children }: PublicLayoutProps) {
    const { shopSettings } = usePage<PageProps>().props;
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    const formatWaDisplay = (num: string) => {
        if (num.startsWith('62')) {
            const rest = num.slice(2);
            return `+62 ${rest.slice(0, 3)}-${rest.slice(3, 7)}-${rest.slice(7)}`;
        }
        return num;
    };

    return (
        <div className="min-h-screen flex flex-col bg-stone-50 text-stone-900 font-sans selection:bg-amber-900 selection:text-white">
            {/* Header / Navbar */}
            <header className="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-stone-200/80 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-20 items-center">
                        {/* Logo */}
                        <div className="shrink-0 flex items-center">
                            <Link href={route('home')} className="flex items-center gap-2 group">
                                <Sofa className="h-5 w-5 text-amber-900 transition duration-300 group-hover:scale-105" />
                                <span className="text-2xl font-bold bg-linear-to-r from-amber-900 to-amber-950 bg-clip-text text-transparent group-hover:from-amber-800 group-hover:to-amber-900 transition duration-300">
                                    Agus Mebel
                                </span>
                                <span className="bg-emerald-800 text-white text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full">
                                    Jepara
                                </span>
                            </Link>
                        </div>

                        {/* Desktop Navigation */}
                        <nav className="hidden md:flex space-x-8">
                            <Link
                                href={route('home')}
                                className={`text-sm font-semibold transition duration-200 ${
                                    route().current('home')
                                        ? 'text-amber-900 border-b-2 border-amber-900 py-1'
                                        : 'text-stone-600 hover:text-amber-900 py-1'
                                }`}
                            >
                                Beranda
                            </Link>
                            <Link
                                href={route('products.index')}
                                className={`text-sm font-semibold transition duration-200 ${
                                    route().current('products.*')
                                        ? 'text-amber-900 border-b-2 border-amber-900 py-1'
                                        : 'text-stone-600 hover:text-amber-900 py-1'
                                }`}
                            >
                                Katalog Produk
                            </Link>
                            <Link
                                href={route('articles.index')}
                                className={`text-sm font-semibold transition duration-200 ${
                                    route().current('articles.*')
                                        ? 'text-amber-900 border-b-2 border-amber-900 py-1'
                                        : 'text-stone-600 hover:text-amber-900 py-1'
                                }`}
                            >
                                Artikel & Info
                            </Link>
                        </nav>

                        {/* Mobile Menu Button */}
                        <div className="flex md:hidden">
                            <button
                                onClick={() => setIsMenuOpen(!isMenuOpen)}
                                className="inline-flex items-center justify-center p-2 rounded-xl text-stone-500 hover:text-stone-900 hover:bg-stone-100 focus:outline-none transition"
                                aria-label="Toggle menu"
                            >
                                {isMenuOpen ? (
                                    <X className="h-6 w-6" />
                                ) : (
                                    <Menu className="h-6 w-6" />
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Mobile Menu */}
                {isMenuOpen && (
                    <div className="md:hidden bg-white border-b border-stone-200">
                        <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                            <Link
                                href={route('home')}
                                className={`block px-3 py-2 rounded-xl text-base font-semibold ${
                                    route().current('home')
                                        ? 'bg-amber-50 text-amber-900'
                                        : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900'
                                }`}
                            >
                                Beranda
                            </Link>
                            <Link
                                href={route('products.index')}
                                className={`block px-3 py-2 rounded-xl text-base font-semibold ${
                                    route().current('products.*')
                                        ? 'bg-amber-50 text-amber-900'
                                        : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900'
                                }`}
                            >
                                Katalog Produk
                            </Link>
                            <Link
                                href={route('articles.index')}
                                className={`block px-3 py-2 rounded-xl text-base font-semibold ${
                                    route().current('articles.*')
                                        ? 'bg-amber-50 text-amber-900'
                                        : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900'
                                }`}
                            >
                                Artikel & Info
                            </Link>

                            <div className="px-3 pt-3">
                                <a
                                    href={`https://wa.me/${waNumber}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="w-full flex items-center justify-center px-4 py-2.5 rounded-xl text-white font-bold text-center shadow-md"
                                    style={{ backgroundColor: '#075E54' }}
                                >
                                    <MessageCircle className="mr-2 h-4 w-4" />
                                    Hubungi Kami
                                </a>
                            </div>
                        </div>
                    </div>
                )}
            </header>

            {/* Main Content */}
            <main className="grow">
                {children}
            </main>

            {/* Footer */}
            <footer className="bg-stone-950 text-stone-400 border-t border-stone-900">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-12">
                        {/* Column 1: Info Toko */}
                        <div className="md:col-span-2 space-y-6">
                            <span className="text-2xl font-bold bg-linear-to-r from-amber-100 to-amber-300 bg-clip-text text-transparent">
                                Agus Mebel Jepara
                            </span>
                            <p className="text-stone-400 text-sm leading-relaxed max-w-md">
                                Kami memproduksi dan menjual berbagai macam produk furniture kayu jati kualitas premium asli dari Jepara. Mulai dari set meja makan, kursi tamu, lemari pakaian, hingga custom design sesuai kebutuhan rumah Anda.
                            </p>
                            <div className="flex gap-4">
                                {/* WhatsApp */}
                                <a href={`https://wa.me/${waNumber}`} target="_blank" rel="noopener noreferrer" className="w-10 h-10 rounded-xl bg-stone-900 hover:bg-emerald-900/40 text-stone-300 hover:text-emerald-400 flex items-center justify-center transition duration-300">
                                    <MessageCircle className="w-5 h-5" />
                                </a>
                            </div>
                        </div>

                        {/* Column 2: Link Navigasi */}
                        <div className="space-y-4">
                            <h3 className="text-stone-200 font-bold text-sm uppercase tracking-wider">Halaman</h3>
                            <ul className="space-y-2.5 text-sm">
                                <li>
                                    <Link href={route('home')} className="hover:text-amber-400 transition duration-200">
                                        Beranda
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('products.index')} className="hover:text-amber-400 transition duration-200">
                                        Katalog Produk
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('articles.index')} className="hover:text-amber-400 transition duration-200">
                                        Artikel & Info
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        {/* Column 3: Jam Kerja / Kontak */}
                        <div className="space-y-4">
                            <h3 className="text-stone-200 font-bold text-sm uppercase tracking-wider">Kontak</h3>
                            <ul className="space-y-2.5 text-sm">
                                <li className="flex gap-2 items-start">
                                    <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                                    <span>Jepara, Jawa Tengah, Indonesia</span>
                                </li>
                                <li className="flex gap-2 items-start">
                                    <Phone className="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                                    <span>{formatWaDisplay(waNumber)}</span>
                                </li>
                                <li className="flex gap-2 items-start">
                                    <Clock3 className="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                                    <span>Senin - Sabtu: 08:00 - 17:00</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {/* Bottom Footer */}
                    <div className="mt-16 pt-8 border-t border-stone-900 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                        <p>&copy; {new Date().getFullYear()} Agus Mebel Jepara. All rights reserved.</p>
                        <p className="flex items-center gap-1">
                            Premium Craftsmanship from Jepara, Indonesia
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
