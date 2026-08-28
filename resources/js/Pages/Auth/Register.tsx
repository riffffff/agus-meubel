import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import AuthModal from '@/Components/Public/AuthModal';

export default function Register() {
    const [isOpen, setIsOpen] = useState(true);

    const handleClose = () => {
        setIsOpen(false);
        if (typeof window !== 'undefined') {
            window.location.href = route('home');
        }
    };

    return (
        <PublicLayout>
            <Head title="Daftar Akun Baru" />
            <AuthModal
                isOpen={isOpen}
                onClose={handleClose}
                initialMode="register"
            />
        </PublicLayout>
    );
}
