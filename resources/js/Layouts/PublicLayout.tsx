import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Clock3,
    LogIn,
    ShoppingCart,
    MapPin,
    MessageCircle,
    Menu,
    Phone,
    X,
    UserPlus,
    LogOut,
} from 'lucide-react';
import { PageProps } from '@/types';
import AuthModal from '@/Components/Public/AuthModal';

interface PublicLayoutProps {
    children: React.ReactNode;
}

export default function PublicLayout({ children }: PublicLayoutProps) {
    const { shopSettings, auth } = usePage<PageProps>().props;
    const user = auth?.user;
    const waNumber = shopSettings?.whatsapp_number || '6281234567890';
    const shopName = shopSettings?.shop_name || 'Agus Mebel Jepara';
    const address = shopSettings?.address || 'Jepara, Jawa Tengah, Indonesia';
    const operatingHours = shopSettings?.operating_hours || 'Senin - Sabtu: 08:00 - 17:00';
    // Logo URL dari setting toko (jika admin upload logo custom, akan pakai itu; fallback ke placeholder default jika null)
    const logoUrl = shopSettings?.logo_url || window.asset('storage/logo/logo.jpeg');
    const logoDarkUrl = shopSettings?.logo_dark_url || logoUrl;

    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
    const [authModalMode, setAuthModalMode] = useState<'login' | 'register'>('login');

    const openAuthModal = (mode: 'login' | 'register') => {
        setAuthModalMode(mode);
        setIsAuthModalOpen(true);
        setIsMenuOpen(false);
    };

    const formatWaDisplay = (num: string) => {
        if (num.startsWith('62')) {
            const rest = num.slice(2);
            return `+62 ${rest.slice(0, 3)}-${rest.slice(3, 7)}-${rest.slice(7)}`;
        }
        return num;
    };

    return (
        <div className="min-h-screen flex flex-col bg-stone-50 text-stone-900 font-sans selection:bg-mahogany-800 selection:text-white">

            {/* ── HEADER ─────────────────────────────────────────────────── */}
            <header className="sticky top-0 z-40 bg-mahogany-900 shadow-lg wood-texture">
                <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-20 items-center">

                        {/* Logo & Nama Toko */}
                        <div className="shrink-0 flex items-center">
                            <Link href={route('home')} className="flex items-center gap-3 group">
                                <div className="h-11 w-11 rounded-xl overflow-hidden bg-white/10 ring-1 ring-white/20 flex items-center justify-center shrink-0 transition group-hover:ring-white/40">
                                    <img
                                        src={logoUrl}
                                        alt={shopName}
                                        className="h-full w-full object-cover"
                                        onError={(e) => {
                                            e.currentTarget.style.display = 'none';
                                        }}
                                    />
                                </div>
                                <span className="text-xl sm:text-2xl font-bold text-white tracking-tight">
                                    {shopName}
                                </span>
                            </Link>
                        </div>

                        {/* Desktop Navigation */}
                        <nav className="hidden md:flex items-center space-x-1">
                            {[
                                { href: route('home'), label: 'Beranda', active: route().current('home') },
                                { href: route('products.index'), label: 'Katalog Produk', active: route().current('products.*') },
                                { href: route('articles.index'), label: 'Artikel & Info', active: route().current('articles.*') },
                            ].map((item) => (
                                <Link
                                    key={item.label}
                                    href={item.href}
                                    className={`px-4 py-2 rounded-lg text-sm font-semibold transition duration-200 ${
                                        item.active
                                            ? 'bg-white/15 text-white'
                                            : 'text-mahogany-200 hover:bg-white/10 hover:text-white'
                                    }`}
                                >
                                    {item.label}
                                </Link>
                            ))}
                        </nav>

                        {/* Desktop Right: Cart + Auth */}
                        <div className="hidden md:flex items-center gap-2">
                            {user ? (
                                <>
                                    <Link
                                        href={route('cart.index')}
                                        className="inline-flex items-center justify-center p-2.5 rounded-lg text-mahogany-200 hover:text-white hover:bg-white/10 transition"
                                        aria-label="Keranjang Belanja"
                                    >
                                        <ShoppingCart className="h-5 w-5" />
                                    </Link>
                                    <span className="text-xs text-mahogany-300 max-w-32 truncate">
                                        Hai, <span className="font-semibold text-white">{user.name}</span>
                                    </span>
                                    <Link
                                        href={route('logout')}
                                        method="post"
                                        as="button"
                                        className="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg text-mahogany-200 hover:text-white hover:bg-white/10 transition font-semibold"
                                    >
                                        <LogOut className="h-4 w-4" />
                                        <span>Keluar</span>
                                    </Link>
                                </>
                            ) : (
                                <>
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('login')}
                                        className="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg text-mahogany-100 hover:text-white hover:bg-white/10 transition font-semibold"
                                    >
                                        <LogIn className="h-4 w-4" />
                                        <span>Masuk</span>
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('register')}
                                        className="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm rounded-lg bg-white text-mahogany-900 hover:bg-mahogany-50 font-bold shadow-sm transition"
                                    >
                                        <UserPlus className="h-4 w-4" />
                                        <span>Daftar</span>
                                    </button>
                                </>
                            )}
                        </div>

                        {/* Mobile: Cart + Hamburger */}
                        <div className="flex md:hidden items-center gap-2">
                            {user && (
                                <Link
                                    href={route('cart.index')}
                                    className="inline-flex items-center justify-center p-2 rounded-lg text-mahogany-200 hover:text-white hover:bg-white/10 transition"
                                    aria-label="Keranjang Belanja"
                                >
                                    <ShoppingCart className="h-5 w-5" />
                                </Link>
                            )}
                            <button
                                onClick={() => setIsMenuOpen(!isMenuOpen)}
                                className="inline-flex items-center justify-center p-2 rounded-lg text-mahogany-200 hover:text-white hover:bg-white/10 transition"
                                aria-label="Toggle menu"
                            >
                                {isMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Mobile Menu */}
                {isMenuOpen && (
                    <div className="md:hidden bg-mahogany-950 border-t border-mahogany-800 wood-texture wood-texture-subtle">
                        <div className="px-3 pt-2 pb-4 space-y-1">
                            {[
                                { href: route('home'), label: 'Beranda', active: route().current('home') },
                                { href: route('products.index'), label: 'Katalog Produk', active: route().current('products.*') },
                                { href: route('articles.index'), label: 'Artikel & Info', active: route().current('articles.*') },
                            ].map((item) => (
                                <Link
                                    key={item.label}
                                    href={item.href}
                                    className={`block px-3 py-2.5 rounded-lg text-base font-semibold transition ${
                                        item.active
                                            ? 'bg-mahogany-800 text-white'
                                            : 'text-mahogany-200 hover:bg-mahogany-800 hover:text-white'
                                    }`}
                                >
                                    {item.label}
                                </Link>
                            ))}

                            <div className="h-px bg-mahogany-800 my-2" />

                            {user ? (
                                <>
                                    <Link
                                        href={route('cart.index')}
                                        className="flex items-center gap-2 px-3 py-2.5 rounded-lg text-base font-semibold text-mahogany-200 hover:bg-mahogany-800 hover:text-white transition"
                                    >
                                        <ShoppingCart className="h-4 w-4" />
                                        Keranjang Saya
                                    </Link>
                                    <div className="px-3 py-1 text-xs text-mahogany-400">
                                        Hai, <span className="font-semibold text-mahogany-200">{user.name}</span>
                                    </div>
                                    <Link
                                        href={route('logout')}
                                        method="post"
                                        as="button"
                                        className="flex items-center gap-2 w-full px-3 py-2.5 rounded-lg text-sm font-bold text-rose-400 hover:bg-rose-950/40 transition"
                                    >
                                        <LogOut className="h-4 w-4" />
                                        Keluar
                                    </Link>
                                </>
                            ) : (
                                <div className="space-y-2 pt-1">
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('login')}
                                        className="flex items-center justify-center gap-1.5 w-full px-4 py-2.5 rounded-lg text-sm font-semibold border border-mahogany-700 text-mahogany-200 hover:bg-mahogany-800 hover:text-white transition"
                                    >
                                        <LogIn className="h-4 w-4" />
                                        Masuk
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('register')}
                                        className="flex items-center justify-center gap-1.5 w-full px-4 py-2.5 rounded-lg text-sm font-bold bg-white text-mahogany-900 hover:bg-mahogany-50 transition"
                                    >
                                        <UserPlus className="h-4 w-4" />
                                        Daftar Akun Baru
                                    </button>
                                </div>
                            )}

                            <div className="pt-2">
                                <a
                                    href={`https://wa.me/${waNumber}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-white font-bold shadow-sm"
                                    style={{ backgroundColor: '#075E54' }}
                                >
                                    <MessageCircle className="h-4 w-4" />
                                    Hubungi Kami via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                )}
            </header>

            {/* ── MAIN CONTENT ───────────────────────────────────────────── */}
            <main className="grow">{children}</main>

            {/* ── FOOTER ─────────────────────────────────────────────────── */}
            <footer className="bg-mahogany-950 text-mahogany-300 border-t border-mahogany-900 wood-texture wood-texture-subtle">
                <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-12">

                        {/* Info Toko */}
                        <div className="md:col-span-2 space-y-5">
                            <div className="flex items-center gap-3">
                                <div className="h-12 w-12 rounded-xl overflow-hidden bg-white/10 ring-1 ring-white/20 flex items-center justify-center shrink-0">
                                    <img
                                        src={window.asset('storage/logo/logo.jpeg')}
                                        alt={shopName}
                                        className="h-full w-full object-cover"
                                        onError={(e) => { e.currentTarget.style.display = 'none'; }}
                                    />
                                </div>
                                <span className="text-2xl font-bold text-white">
                                    {shopName}
                                </span>
                            </div>
                            <p className="text-mahogany-400 text-sm leading-relaxed max-w-md">
                                Kami memproduksi furniture kayu jati kualitas terbaik langsung dari pengrajin Jepara. Melayani pemesanan retail maupun custom order.
                            </p>
                            <a
                                href={`https://wa.me/${waNumber}`}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="inline-flex items-center gap-2 w-10 h-10 rounded-xl bg-mahogany-900 hover:bg-emerald-900/50 text-mahogany-300 hover:text-emerald-400 justify-center transition duration-300"
                            >
                                <MessageCircle className="w-5 h-5" />
                            </a>
                        </div>

                        {/* Navigasi */}
                        <div className="space-y-4">
                            <h3 className="text-white font-bold text-sm uppercase tracking-wider">Halaman</h3>
                            <ul className="space-y-2.5 text-sm">
                                {[
                                    { href: route('home'), label: 'Beranda' },
                                    { href: route('products.index'), label: 'Katalog Produk' },
                                    { href: route('articles.index'), label: 'Artikel & Info' },
                                ].map((item) => (
                                    <li key={item.label}>
                                        <Link
                                            href={item.href}
                                            className="hover:text-mahogany-100 transition duration-200"
                                        >
                                            {item.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        {/* Kontak */}
                        <div className="space-y-4">
                            <h3 className="text-white font-bold text-sm uppercase tracking-wider">Kontak Toko</h3>
                            <ul className="space-y-3 text-sm">
                                <li className="flex gap-2.5 items-start">
                                    <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-mahogany-500" />
                                    <span>{address}</span>
                                </li>
                                <li className="flex gap-2.5 items-start">
                                    <Phone className="mt-0.5 h-4 w-4 shrink-0 text-mahogany-500" />
                                    <span>{formatWaDisplay(waNumber)}</span>
                                </li>
                                <li className="flex gap-2.5 items-start">
                                    <Clock3 className="mt-0.5 h-4 w-4 shrink-0 text-mahogany-500" />
                                    <span>{operatingHours}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {/* Bottom bar */}
                    <div className="mt-14 pt-8 border-t border-mahogany-900 flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-mahogany-500">
                        <p>&copy; {new Date().getFullYear()} {shopName}. All rights reserved.</p>
                        <p>High Quality Teak Furniture — Jepara</p>
                    </div>
                </div>
            </footer>

            {/* Auth Modal */}
            <AuthModal
                isOpen={isAuthModalOpen}
                onClose={() => setIsAuthModalOpen(false)}
                initialMode={authModalMode}
            />

            {/* Floating WhatsApp */}
            <style>{`
                @keyframes kembangKempis {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.04); }
                }
                .animate-kembang-kempis {
                    animation: kembangKempis 3s ease-in-out infinite;
                }
            `}</style>
            <a
                href={`https://wa.me/${waNumber}?text=${encodeURIComponent('Halo, saya ingin konsultasi mengenai produk mebel.')}`}
                target="_blank"
                rel="noopener noreferrer"
                className="fixed bottom-10 sm:bottom-12 right-6 sm:right-8 z-50 flex items-center gap-3 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl border border-stone-200/80 shadow-xl transition-all duration-300 group animate-kembang-kempis hover:animate-none hover:scale-105"
                aria-label="Konsultasi WhatsApp"
            >
                <div className="relative flex items-center justify-center w-10 h-10 rounded-xl bg-[#25D366] text-white shadow-md group-hover:scale-110 transition-transform duration-300 shrink-0">
                    <MessageCircle className="w-5 h-5 fill-white/20" />
                    <span className="absolute -top-1 -right-1 flex h-3 w-3">
                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#25D366] opacity-75"></span>
                        <span className="relative inline-flex rounded-full h-3 w-3 bg-[#128C7E]"></span>
                    </span>
                </div>
                <div className="flex flex-col text-left">
                    <span className="text-xs font-extrabold text-stone-900 leading-tight">Ingin konsultasi?</span>
                    <span className="text-[11px] font-semibold text-emerald-700 leading-tight">Hubungi kami di WhatsApp</span>
                </div>
            </a>
        </div>
    );
}
