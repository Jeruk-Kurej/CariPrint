import { Link } from '@inertiajs/react';

/** Tanda registrasi cetak: lingkaran dengan garis silang, dipakai sebagai logo. */
function RegistrationMark() {
    return (
        <svg
            viewBox="0 0 24 24"
            className="size-6 text-brand"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.75"
            aria-hidden
        >
            <circle cx="12" cy="12" r="7.5" />
            <path d="M12 1.5v5M12 17.5v5M1.5 12h5M17.5 12h5" />
            <circle cx="12" cy="12" r="2.25" fill="currentColor" />
        </svg>
    );
}

export function PublicHeader() {
    return (
        <header className="sticky top-0 z-10 border-b bg-background/95 backdrop-blur">
            <div className="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
                <Link href="/" className="flex items-center gap-2">
                    <RegistrationMark />
                    <span className="font-display text-lg font-bold tracking-tight">
                        CariPrint
                    </span>
                </Link>
                <nav className="flex items-center gap-4 text-sm font-medium">
                    <Link
                        href="/"
                        className="text-muted-foreground hover:text-foreground"
                    >
                        Cari tempat
                    </Link>
                    <Link
                        href="/agent"
                        className="text-muted-foreground hover:text-foreground"
                    >
                        Asisten cetak
                    </Link>
                </nav>
            </div>
        </header>
    );
}
