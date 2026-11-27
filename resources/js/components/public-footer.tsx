import { Link } from '@inertiajs/react';
import { BrandMark } from '@/components/brand-mark';
import TextLink from '@/components/text-link';
import { footerNav, homeUrl } from '@/lib/navigation';

export function PublicFooter() {
    return (
        <footer className="mt-16 border-t bg-card">
            <div className="mx-auto max-w-[90rem] px-4 lg:px-10">
                <div className="flex flex-col gap-4 py-6 md:flex-row md:items-center md:justify-between">
                    <div className="flex flex-col gap-1">
                        <Link
                            href={homeUrl}
                            className="inline-flex min-h-11 w-fit items-center gap-2 font-display text-base font-bold tracking-tight text-foreground"
                        >
                            <BrandMark />
                            CariPrint
                        </Link>
                        <p className="max-w-sm text-sm text-muted-foreground">
                            Harga, jam buka, dan jarak tempat print di sekitar
                            CitraLand Surabaya.
                        </p>
                    </div>

                    <nav aria-label="Informasi">
                        <ul className="flex flex-wrap gap-x-6">
                            {footerNav.map((link) => (
                                <li key={link.href}>
                                    <TextLink
                                        href={link.href}
                                        className="inline-flex min-h-11 items-center text-sm font-medium"
                                    >
                                        {link.label}
                                    </TextLink>
                                </li>
                            ))}
                        </ul>
                    </nav>
                </div>

                <div className="flex flex-col gap-1 border-t py-4 text-xs text-muted-foreground sm:flex-row sm:justify-between">
                    <p>© {new Date().getFullYear()} CariPrint</p>
                    <p>Pesan dan bayar langsung ke tempatnya.</p>
                </div>
            </div>
        </footer>
    );
}
