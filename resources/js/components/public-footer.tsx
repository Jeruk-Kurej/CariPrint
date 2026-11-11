import { Link } from '@inertiajs/react';
import { index as agentIndex } from '@/routes/agent';

export function PublicFooter() {
    return (
        <footer className="mt-16 border-t bg-card">
            <div className="mx-auto grid max-w-6xl gap-8 px-4 py-10 text-sm sm:grid-cols-[1.5fr_1fr_1fr]">
                <div className="flex flex-col gap-2">
                    <p className="font-display text-lg font-bold tracking-tight">
                        CariPrint
                    </p>
                    <p className="max-w-sm text-muted-foreground">
                        Menghubungkan mahasiswa dengan tempat printing di
                        sekitar CitraLand Surabaya. Tidak ada pemesanan maupun
                        pembayaran di website ini.
                    </p>
                </div>
                <nav aria-label="Halaman" className="flex flex-col gap-2">
                    <p className="font-display font-semibold">Halaman</p>
                    <Link
                        href="/"
                        className="text-muted-foreground hover:text-foreground"
                    >
                        Cari tempat
                    </Link>
                    <Link
                        href={agentIndex().url}
                        className="text-muted-foreground hover:text-foreground"
                    >
                        Asisten cetak
                    </Link>
                </nav>
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
