import { Link } from '@inertiajs/react';
import { aboutNav, homeUrl, mainNav } from '@/lib/navigation';

function FooterColumn({
    title,
    links,
}: {
    title: string;
    links: { label: string; href: string }[];
}) {
    return (
        <nav aria-label={title} className="flex flex-col gap-2">
            <p className="font-display font-semibold">{title}</p>
            {links.map((link) => (
                <Link
                    key={link.href}
                    href={link.href}
                    className="text-muted-foreground hover:text-foreground"
                >
                    {link.label}
                </Link>
            ))}
        </nav>
    );
}

export function PublicFooter() {
    return (
        <footer className="mt-16 border-t bg-card">
            <div className="mx-auto grid max-w-6xl gap-8 px-4 py-10 text-sm sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.2fr]">
                <div className="flex flex-col gap-2">
                    <Link
                        href={homeUrl}
                        className="font-display text-lg font-bold tracking-tight"
                    >
                        CariPrint
                    </Link>
                    <p className="max-w-sm text-muted-foreground">
                        Menghubungkan mahasiswa dengan tempat printing di
                        sekitar CitraLand Surabaya. Tidak ada pemesanan maupun
                        pembayaran di website ini.
                    </p>
                </div>
                <FooterColumn title="Fitur" links={mainNav.slice(0, 3)} />
                <FooterColumn title="Informasi" links={aboutNav} />
                <div className="flex flex-col gap-2">
                    <p className="font-display font-semibold">Catatan</p>
                    <p className="text-muted-foreground">
                        Lokasimu hanya dipakai untuk menghitung jarak dan tidak
                        disimpan.
                    </p>
                    <p className="text-muted-foreground">
                        Proyek Milestone 1, Universitas Ciputra.
                    </p>
                </div>
            </div>
        </footer>
    );
}
