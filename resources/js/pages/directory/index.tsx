import { Head } from '@inertiajs/react';
import { ShopCard } from '@/components/directory/shop-card';
import type { ShopSummary } from '@/types';

const sampleShops: ShopSummary[] = [
    {
        id: 1,
        slug: 'contoh-print-a',
        name: 'Contoh Print A',
        address: 'Jl. Contoh No. 1, CitraLand',
        whatsapp: '6281234567890',
        openState: 'open',
        distanceKm: 0.4,
        startingPrice: 300,
    },
    {
        id: 2,
        slug: 'contoh-print-b',
        name: 'Contoh Print B',
        address: 'Jl. Contoh No. 2, CitraLand',
        whatsapp: null,
        openState: 'closing_soon',
        distanceKm: 1.2,
        startingPrice: 500,
    },
    {
        id: 3,
        slug: 'contoh-print-c',
        name: 'Contoh Print C',
        address: 'Jl. Contoh No. 3, CitraLand',
        whatsapp: '6281234567891',
        openState: 'closed',
        distanceKm: null,
        startingPrice: null,
    },
];

export default function DirectoryIndex() {
    return (
        <>
            <Head title="Cari tempat printing" />
            <main className="mx-auto flex w-full max-w-3xl flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">
                    Tempat printing di sekitar CitraLand
                </h1>
                <div className="grid gap-3 sm:grid-cols-2">
                    {sampleShops.map((shop) => (
                        <ShopCard key={shop.id} shop={shop} />
                    ))}
                </div>
            </main>
        </>
    );
}
