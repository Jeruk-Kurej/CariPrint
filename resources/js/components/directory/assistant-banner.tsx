import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { index as agentIndex } from '@/routes/agent';

/** Ajakan ke asisten cetak, dengan contoh kalimat yang bisa ditulis pengguna. */
export function AssistantBanner() {
    return (
        <section
            aria-labelledby="asisten"
            className="grid items-center gap-6 rounded-lg bg-accent p-6 sm:p-8 md:grid-cols-[1.2fr_1fr]"
        >
            <div className="flex flex-col gap-3">
                <h2
                    id="asisten"
                    className="font-display text-2xl font-bold tracking-tight sm:text-3xl"
                >
                    Bingung hitung biayanya?
                </h2>
                <p className="max-w-md text-muted-foreground">
                    Tulis kebutuhan cetakmu dengan kalimat biasa. Asisten
                    menyusun spesifikasinya, lalu biaya dihitung dari daftar
                    harga tiap tempat.
                </p>
                <div>
                    <Button asChild>
                        <Link href={agentIndex().url}>Coba asisten cetak</Link>
                    </Button>
                </div>
            </div>
            <p className="rounded-lg border bg-card px-4 py-3 text-sm shadow-sm">
                <span className="text-muted-foreground">Contoh: </span>
                “Print halaman 1-10, hitam putih, A4, jilid spiral. Cari yang
                paling murah.”
            </p>
        </section>
    );
}
