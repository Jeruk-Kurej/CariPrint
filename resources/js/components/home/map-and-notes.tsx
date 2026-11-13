import { Link, router } from '@inertiajs/react';
import { ShopMap } from '@/components/directory/shop-map';
import { index as agentIndex } from '@/routes/agent';
import { index as compareIndex } from '@/routes/compare';
import { show } from '@/routes/directory';
import { howItWorks } from '@/routes/info';
import type { ShopSummary } from '@/types';

const notes = [
    {
        title: 'Bandingkan harga',
        text: 'Harga print, fotokopi, dan jilid dari semua tempat dalam satu tabel.',
        href: compareIndex().url,
        paper: 'bg-note-yellow -rotate-1',
    },
    {
        title: 'Asisten cetak',
        text: 'Tulis kebutuhanmu dengan kalimat biasa, lihat estimasi biaya tiap tempat.',
        href: agentIndex().url,
        paper: 'bg-note-pink rotate-1',
    },
    {
        title: 'Cara kerja',
        text: 'Apa arti Buka, Perkiraan, dan jarak, dan siapa yang menghitungnya.',
        href: howItWorks().url,
        paper: 'bg-note-mint -rotate-[0.6deg]',
    },
];

/** Peta yang bisa dipakai langsung, ditemani tiga sticky note ke fitur lain. */
export function MapAndNotes({ shops }: { shops: ShopSummary[] }) {
    function open(id: number) {
        const shop = shops.find((item) => item.id === id);

        if (shop) {
            router.visit(show({ shop: shop.slug }).url);
        }
    }

    return (
        <section
            aria-label="Peta dan fitur lain"
            className="grid gap-8 px-4 py-10 lg:grid-cols-[1.4fr_1fr]"
        >
            <div className="flex flex-col gap-3">
                <h2 className="font-display text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Lihat di peta
                </h2>
                <ShopMap
                    shops={shops}
                    selectedId={null}
                    onSelect={open}
                    heightClass="h-80 lg:h-[26rem]"
                />
                <p className="text-sm text-muted-foreground">
                    Klik sebuah titik untuk membuka halaman tempatnya.
                </p>
            </div>
            <ul className="flex flex-col justify-center gap-5">
                {notes.map((note) => (
                    <li key={note.title}>
                        <Link
                            href={note.href}
                            className={`dog-ear relative block rounded-sm p-5 paper-shadow hover:-translate-y-1 hover:rotate-0 motion-safe:transition-transform ${note.paper}`}
                        >
                            <h3 className="font-display text-2xl font-extrabold">
                                {note.title}
                            </h3>
                            <p className="mt-1 text-sm text-foreground/80">
                                {note.text}
                            </p>
                        </Link>
                    </li>
                ))}
            </ul>
        </section>
    );
}
