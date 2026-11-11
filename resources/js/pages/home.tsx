import { Head } from '@inertiajs/react';
import { FlyerBoard } from '@/components/home/flyer-board';
import { HomeHero } from '@/components/home/home-hero';
import { MapAndNotes } from '@/components/home/map-and-notes';
import { splitOpenShops } from '@/lib/featured-shops';
import type { ShopSummary } from '@/types';

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
