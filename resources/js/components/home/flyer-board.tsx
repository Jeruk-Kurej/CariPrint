import { Link } from '@inertiajs/react';
import { FlyerCard } from '@/components/directory/flyer-card';
import { index as directoryIndex } from '@/routes/directory';
import type { ShopSummary } from '@/types';

type Props = {
    /** Tampil di hero pada layar lebar, jadi di papan hanya muncul di layar kecil. */
    featured: ShopSummary[];
    shops: ShopSummary[];
};

/** Papan pengumuman: tempat yang masih buka. */
export function FlyerBoard({ featured, shops }: Props) {
    if (featured.length + shops.length === 0) {
        return null;
    }

    return (
        <section
            aria-labelledby="papan"
            className="flex flex-col gap-6 px-4 py-6"
        >
            <div className="flex flex-wrap items-end justify-between gap-3">
                <h2
                    id="papan"
                    className="font-display text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    Yang masih buka sekarang
                </h2>
                <Link
                    href={directoryIndex().url}
                    className="text-sm font-semibold text-primary underline underline-offset-4"
                >
                    Lihat semua tempat
                </Link>
            </div>
            <div className="grid gap-7 pt-3 sm:grid-cols-2 lg:grid-cols-3">
                {featured.map((shop) => (
                    <div key={shop.id} className="lg:hidden">
                        <FlyerCard shop={shop} />
                    </div>
                ))}
                {shops.slice(0, 6).map((shop) => (
                    <FlyerCard key={shop.id} shop={shop} />
                ))}
            </div>
        </section>
    );
}
