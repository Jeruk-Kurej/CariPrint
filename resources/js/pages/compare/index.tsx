import { Head } from '@inertiajs/react';
import { CompareTable } from '@/components/compare/compare-table';
import type {
    CompareRow,
    CompareShop,
} from '@/components/compare/compare-table';
import { EmptyState } from '@/components/directory/empty-state';

type Props = {
    shops: CompareShop[];
    rows: CompareRow[];
};

export default function CompareIndex({ shops, rows }: Props) {
    return (
        <>
            <Head title="Bandingkan harga" />
            <main className="flex flex-col gap-6 px-4 py-8 sm:py-12">
                <header className="flex flex-col gap-2">
                    <h1 className="font-display text-3xl leading-tight font-extrabold tracking-tight sm:text-5xl">
                        Bandingkan harga
                    </h1>
                    <p className="max-w-2xl text-muted-foreground">
                        Harga layanan yang sama dari semua tempat, berdampingan.
                        Yang paling murah di tiap baris diberi warna kuning.
                        Tanda – berarti tempat itu belum punya harganya.
                    </p>
                </header>

                {rows.length === 0 ? (
                    <EmptyState
                        title="Belum ada harga untuk dibandingkan"
                        hint="Data sedang disiapkan oleh tim survei."
                    />
                ) : (
                    <>
                        <CompareTable shops={shops} rows={rows} />
                        <p className="text-sm text-muted-foreground">
                            Harga masih berupa perkiraan dan bisa berbeda saat
                            dicek langsung. Buka halaman tempatnya untuk melihat
                            sumber dan tanggal pembaruan tiap harga.
                        </p>
                    </>
                )}
            </main>
        </>
    );
}
