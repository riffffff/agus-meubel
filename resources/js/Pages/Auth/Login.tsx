import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import AuthModal from '@/Components/Public/AuthModal';

export default function Login() {
    const [isOpen, setIsOpen] = useState(true);

    const handleClose = () => {
        setIsOpen(false);
        if (typeof window !== 'undefined') {
            window.location.href = route('home');
        }
    };

    return (
        <PublicLayout>
            <Head title="Masuk ke Akun" />
            <AuthModal
                isOpen={isOpen}
                onClose={handleClose}
                initialMode="login"
            />
        </PublicLayout>
    );
}
