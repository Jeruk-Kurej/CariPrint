import { Head } from '@inertiajs/react';
import { CompareTable } from '@/components/compare/compare-table';
import type {
    CompareRow,
    CompareShop,
} from '@/components/compare/compare-table';
import { EmptyState } from '@/components/directory/empty-state';
import { PageHeader } from '@/components/page-header';

type Props = { shops: CompareShop[]; rows: CompareRow[] };

export default function CompareIndex({ shops, rows }: Props) {
    return (
        <>
            <Head title="Bandingkan harga" />
            <main className="flex flex-col gap-6 px-4 py-8 sm:py-12">
                <PageHeader
                    title="Bandingkan harga"
                    intro="Harga layanan yang sama dari semua tempat, berdampingan. Yang paling murah di tiap baris diberi warna kuning. Tanda – berarti tempat itu belum punya harganya."
                />

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
