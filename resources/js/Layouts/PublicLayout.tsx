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
    Sofa,
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
        <div className="min-h-screen flex flex-col bg-stone-50 text-stone-900 font-sans selection:bg-amber-900 selection:text-white">
            {/* Header / Navbar */}
            <header className="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-stone-200/80 shadow-xs">
                <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-20 items-center">
                        {/* Logo & Shop Name */}
                        <div className="shrink-0 flex items-center">
                            <Link href={route('home')} className="flex items-center gap-2.5 group">
                                <div className="p-2 rounded-xl bg-amber-900 text-amber-50 shadow-xs group-hover:bg-amber-800 transition">
                                    <Sofa className="h-5 w-5" />
                                </div>
                                <span className="text-xl sm:text-2xl font-bold bg-linear-to-r from-amber-900 to-amber-950 bg-clip-text text-transparent">
                                    {shopName}
                                </span>
                            </Link>
                        </div>

                        {/* Desktop Navigation */}
                        <nav className="hidden md:flex items-center space-x-8">
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

                        {/* Desktop Right Actions: Cart + Auth */}
                        <div className="hidden md:flex items-center gap-3">
                            {user ? (
                                <>
                                    <Link
                                        href={route('cart.index')}
                                        className="relative inline-flex items-center justify-center p-2.5 rounded-xl text-stone-600 hover:text-amber-900 hover:bg-amber-50 transition focus:outline-none"
                                        aria-label="Keranjang Belanja"
                                    >
                                        <ShoppingCart className="h-5 w-5" />
                                    </Link>
                                    <div className="text-xs text-stone-500 max-w-36 truncate">
                                        Hai, <span className="font-semibold text-stone-800">{user.name}</span>
                                    </div>
                                    <form
                                        method="POST"
                                        action={route('logout')}
                                        onSubmit={(e) => {
                                            e.preventDefault();
                                            (window as any).Inertia?.post(route('logout'), {
                                                onFinish: () => window.location.reload(),
                                            });
                                        }}
                                    >
                                        <button
                                            type="submit"
                                            className="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-xl text-stone-600 hover:text-rose-700 hover:bg-rose-50 transition font-semibold"
                                        >
                                            <LogOut className="h-4 w-4" />
                                            <span>Keluar</span>
                                        </button>
                                    </form>
                                </>
                            ) : (
                                <>
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('login')}
                                        className="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm rounded-xl text-stone-700 hover:text-amber-900 hover:bg-stone-100 transition font-semibold"
                                    >
                                        <LogIn className="h-4 w-4 text-stone-500" />
                                        <span>Masuk</span>
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('register')}
                                        className="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm rounded-xl bg-amber-900 text-white hover:bg-amber-800 font-bold shadow-xs transition"
                                    >
                                        <UserPlus className="h-4 w-4" />
                                        <span>Daftar</span>
                                    </button>
                                </>
                            )}
                        </div>

                        {/* Mobile Menu Button */}
                        <div className="flex md:hidden items-center gap-2">
                            {user && (
                                <Link
                                    href={route('cart.index')}
                                    className="inline-flex items-center justify-center p-2 rounded-xl text-stone-600 hover:bg-amber-50 hover:text-amber-900 transition"
                                    aria-label="Keranjang Belanja"
                                >
                                    <ShoppingCart className="h-5 w-5" />
                                </Link>
                            )}
                            <button
                                onClick={() => setIsMenuOpen(!isMenuOpen)}
                                className="inline-flex items-center justify-center p-2 rounded-xl text-stone-500 hover:text-stone-900 hover:bg-stone-100 focus:outline-none transition"
                                aria-label="Toggle menu"
                            >
                                {isMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Mobile Menu */}
                {isMenuOpen && (
                    <div className="md:hidden bg-white border-b border-stone-200">
                        <div className="px-3 pt-2 pb-4 space-y-1">
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

                            {user ? (
                                <>
                                    <Link
                                        href={route('cart.index')}
                                        className="block mt-2 px-3 py-2 rounded-xl text-base font-semibold text-stone-600 hover:bg-amber-50 hover:text-amber-900"
                                    >
                                        <span className="inline-flex items-center gap-2">
                                            <ShoppingCart className="h-4 w-4" />
                                            Keranjang Saya
                                        </span>
                                    </Link>
                                    <div className="px-3 pt-2 text-xs text-stone-500">
                                        Hai, <span className="font-semibold text-stone-700">{user.name}</span>
                                    </div>
                                    <div className="px-3 pt-1">
                                        <form
                                            method="POST"
                                            action={route('logout')}
                                            onSubmit={(e) => {
                                                e.preventDefault();
                                                (window as any).Inertia?.post(route('logout'), {
                                                    onFinish: () => window.location.reload(),
                                                });
                                            }}
                                        >
                                            <button
                                                type="submit"
                                                className="w-full inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold text-rose-700 hover:bg-rose-50 transition"
                                            >
                                                <LogOut className="h-4 w-4" />
                                                Keluar
                                            </button>
                                        </form>
                                    </div>
                                </>
                            ) : (
                                <div className="px-3 pt-3 space-y-2">
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('login')}
                                        className="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold border border-stone-300 text-stone-700 hover:bg-stone-50"
                                    >
                                        <span className="inline-flex items-center justify-center gap-1.5">
                                            <LogIn className="h-4 w-4" />
                                            Masuk
                                        </span>
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => openAuthModal('register')}
                                        className="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-900 hover:bg-amber-800 shadow-xs"
                                    >
                                        <span className="inline-flex items-center justify-center gap-1.5">
                                            <UserPlus className="h-4 w-4" />
                                            Daftar Akun Baru
                                        </span>
                                    </button>
                                </div>
                            )}

                            <div className="px-3 pt-3">
                                <a
                                    href={`https://wa.me/${waNumber}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="w-full flex items-center justify-center px-4 py-2.5 rounded-xl text-white font-bold text-center shadow-xs"
                                    style={{ backgroundColor: '#075E54' }}
                                >
                                    <MessageCircle className="mr-2 h-4 w-4" />
                                    Hubungi Kami via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                )}
            </header>

            {/* Main Content */}
            <main className="grow">{children}</main>

            {/* Footer */}
            <footer className="bg-stone-950 text-stone-400 border-t border-stone-900">
                <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-12">
                        {/* Column 1: Info Toko (Dynamic from shopSettings) */}
                        <div className="md:col-span-2 space-y-6">
                            <span className="text-2xl font-bold bg-linear-to-r from-amber-100 to-amber-300 bg-clip-text text-transparent">
                                {shopName}
                            </span>
                            <p className="text-stone-400 text-sm leading-relaxed max-w-md">
                                Kami memproduksi furniture kayu jati kualitas terbaik langsung dari pengrajin Jepara. Melayani pemesanan retail maupun custom order.
                            </p>
                            <div className="flex gap-4">
                                <a
                                    href={`https://wa.me/${waNumber}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="w-10 h-10 rounded-xl bg-stone-900 hover:bg-emerald-900/40 text-stone-300 hover:text-emerald-400 flex items-center justify-center transition duration-300"
                                >
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

                        {/* Column 3: Jam Kerja / Kontak (Dynamic from shopSettings) */}
                        <div className="space-y-4">
                            <h3 className="text-stone-200 font-bold text-sm uppercase tracking-wider">Kontak Toko</h3>
                            <ul className="space-y-2.5 text-sm">
                                <li className="flex gap-2 items-start">
                                    <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                                    <span>{address}</span>
                                </li>
                                <li className="flex gap-2 items-start">
                                    <Phone className="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                                    <span>{formatWaDisplay(waNumber)}</span>
                                </li>
                                <li className="flex gap-2 items-start">
                                    <Clock3 className="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                                    <span>{operatingHours}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {/* Bottom Footer */}
                    <div className="mt-16 pt-8 border-t border-stone-900 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                        <p>&copy; {new Date().getFullYear()} {shopName}. All rights reserved.</p>
                        <p className="flex items-center gap-1">
                            High Quality Teak Furniture
                        </p>
                    </div>
                </div>
            </footer>

            {/* Global Auth Modal */}
            <AuthModal
                isOpen={isAuthModalOpen}
                onClose={() => setIsAuthModalOpen(false)}
                initialMode={authModalMode}
            />
        </div>
    );
}
