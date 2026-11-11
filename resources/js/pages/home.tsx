import { Head } from '@inertiajs/react';
import { FlyerBoard } from '@/components/home/flyer-board';
import { HomeHero } from '@/components/home/home-hero';
import { MapAndNotes } from '@/components/home/map-and-notes';
import type { ShopSummary } from '@/types';

const FEATURED = 3;

/** Yang buka dan paling murah tampil di hero. Papan menampilkan sisanya supaya tidak diulang. */
function splitOpenShops(shops: ShopSummary[]) {
    const open = shops
        .filter((shop) => shop.openState !== 'closed')
        .sort(
            (a, b) =>
                (a.startingPrice ?? Infinity) - (b.startingPrice ?? Infinity),
        );

    return { featured: open.slice(0, FEATURED), rest: open.slice(FEATURED) };
}

export default function Home({ shops }: { shops: ShopSummary[] }) {
    const { featured, rest } = splitOpenShops(shops);

    return (
        <>
            <Head title="Cari tempat printing di CitraLand" />
            <main className="flex flex-col pb-4">
                <HomeHero shops={shops} featured={featured} />
                <FlyerBoard featured={featured} shops={rest} />
                <MapAndNotes shops={shops} />
            </main>
        </>
    );
}
