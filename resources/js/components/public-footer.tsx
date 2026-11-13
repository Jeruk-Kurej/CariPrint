import { Link } from '@inertiajs/react';
import { homeUrl, mainNav } from '@/lib/navigation';

export function PublicFooter() {
    return (
        <footer className="mt-16 border-t-2 border-dashed bg-card">
            <div className="mx-auto flex max-w-[90rem] flex-col justify-between gap-8 px-4 py-10 text-sm sm:flex-row sm:items-start lg:px-10">
                <div className="flex flex-col gap-2">
                    <Link
                        href={homeUrl}
                        className="font-display text-lg font-bold tracking-tight"
                    >
                        CariPrint
                    </Link>
                    <p className="max-w-md text-muted-foreground">
                        Menghubungkan mahasiswa dengan tempat printing di
                        sekitar CitraLand Surabaya. Tidak ada pemesanan maupun
                        pembayaran di website ini.
                    </p>
                </div>

                <nav
                    aria-label="Menu footer"
                    className="flex flex-wrap gap-x-6 gap-y-2 font-medium"
                >
                    {mainNav.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className="text-muted-foreground hover:text-foreground"
                        >
                            {link.label}
                        </Link>
                    ))}
                </nav>
            </div>
        </footer>
    );
}
