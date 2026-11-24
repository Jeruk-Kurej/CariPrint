import { Link } from '@inertiajs/react';
import { BrandMark } from '@/components/brand-mark';
import { homeUrl, privacyUrl } from '@/lib/navigation';

export function PublicFooter() {
    return (
        <footer className="mt-16 border-t bg-card">
            <div className="mx-auto flex max-w-[90rem] flex-col items-start justify-between gap-3 px-4 py-6 text-sm text-muted-foreground sm:flex-row sm:items-center lg:px-10">
                <div className="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <Link
                        href={homeUrl}
                        className="inline-flex min-h-11 items-center gap-2 font-display text-base font-bold tracking-tight text-foreground"
                    >
                        <BrandMark />
                        CariPrint
                    </Link>
                    <span aria-hidden className="hidden sm:inline">
                        ·
                    </span>
                    <span>
                        Harga, jam buka, dan jarak tempat print di sekitar
                        CitraLand Surabaya.
                    </span>
                </div>

                <p className="text-xs sm:text-sm">
                    © {new Date().getFullYear()} CariPrint · Pesan dan bayar
                    langsung ke tempatnya ·{' '}
                    <Link
                        href={privacyUrl}
                        className="-my-3.5 inline-block py-3.5 font-medium text-foreground hover:underline"
                    >
                        Privasi
                    </Link>
                </p>
            </div>
        </footer>
    );
}
