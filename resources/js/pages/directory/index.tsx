import { Head, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { EmptyState } from '@/components/directory/empty-state';
import { ShopList } from '@/components/directory/shop-list';
import { ShopMap } from '@/components/directory/shop-map';
import { useGeolocation } from '@/hooks/use-geolocation';
import { useSelectedShop } from '@/hooks/use-selected-shop';
import { nearby } from '@/routes/directory';
import type { ShopSummary } from '@/types';

export default function DirectoryIndex({ shops }: { shops: ShopSummary[] }) {
    const location = useGeolocation();
    const { selectedId, select } = useSelectedShop();
    const [sorting, setSorting] = useState(false);

    useEffect(() => {
        if (location.status !== 'granted') {
            return;
        }

        setSorting(true);
        router.post(
            nearby().url,
            { latitude: location.latitude, longitude: location.longitude },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['shops'],
                onFinish: () => setSorting(false),
            },
        );
    }, [location]);

    const sortedByDistance = location.status === 'granted';

    return (
        <>
            <Head title="Cari tempat printing" />
            <main className="flex flex-col gap-6 px-4 py-6">
                <section className="flex flex-col gap-2">
                    <p className="text-xs font-semibold tracking-wide text-brand uppercase">
                        Area CitraLand Surabaya
                    </p>
                    <h1 className="text-2xl leading-tight font-bold sm:text-3xl">
                        Bandingkan tempat printing sebelum jalan ke sana
                    </h1>
                    <p className="max-w-2xl text-sm text-muted-foreground">
                        Lihat siapa yang masih buka, harga mulai dari, dan
                        jaraknya dari kamu. Tidak perlu telepon satu per satu.
                    </p>
                    <div className="mt-1 flex flex-wrap items-center gap-2 text-xs">
                        <span className="rounded-full border px-3 py-1 font-medium">
                            {shops.length} tempat
                        </span>
                        <span className="rounded-full border px-3 py-1 text-muted-foreground">
                            {sortedByDistance
                                ? 'Urut dari yang terdekat'
                                : 'Urut nama · izinkan lokasi untuk urut jarak'}
                        </span>
                    </div>
                </section>

                {shops.length === 0 ? (
                    <EmptyState
                        title="Belum ada tempat printing"
                        hint="Data sedang disiapkan oleh tim survei."
                    />
                ) : (
                    <div className="grid min-w-0 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                        <ShopList
                            shops={shops}
                            selectedId={selectedId}
                            onSelect={select}
                            sorting={sorting}
                        />
                        <div className="lg:sticky lg:top-20">
                            <ShopMap
                                shops={shops}
                                selectedId={selectedId}
                                onSelect={select}
                            />
                        </div>
                    </div>
                )}
            </main>
        </>
    );
}
