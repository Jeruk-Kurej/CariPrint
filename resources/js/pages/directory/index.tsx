import { Head, router } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { AssistantBanner } from '@/components/directory/assistant-banner';
import { EmptyState } from '@/components/directory/empty-state';
import { HomeHero } from '@/components/directory/home-hero';
import { HowItWorks } from '@/components/directory/how-it-works';
import { ShopList } from '@/components/directory/shop-list';
import { ShopMap } from '@/components/directory/shop-map';
import { Button } from '@/components/ui/button';
import { useGeolocation } from '@/hooks/use-geolocation';
import { useSelectedShop } from '@/hooks/use-selected-shop';
import { filterAndSortShops, type ShopSort } from '@/lib/shop-filter';
import { nearby } from '@/routes/directory';
import type { ShopSummary } from '@/types';

const sortLabels: Record<ShopSort, string> = {
    distance: 'Terdekat',
    price: 'Termurah',
    name: 'Nama',
};

export default function DirectoryIndex({ shops }: { shops: ShopSummary[] }) {
    const location = useGeolocation();
    const { selectedId, select } = useSelectedShop();
    const [sorting, setSorting] = useState(false);
    const [openOnly, setOpenOnly] = useState(false);
    const [chosenSort, setChosenSort] = useState<ShopSort | null>(null);

    const hasDistance = location.status === 'granted';
    const sort = chosenSort ?? (hasDistance ? 'distance' : 'name');

    useEffect(() => {
        if (!hasDistance) {
            return;
        }

        setSorting(true);
        router.post(
            nearby().url,
            { latitude: location.latitude, longitude: location.longitude },
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ['shops'],
                onFinish: () => setSorting(false),
            },
        );
    }, [location]);

    const visibleShops = useMemo(
        () => filterAndSortShops(shops, { openOnly, sort }),
        [shops, openOnly, sort],
    );
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    return (
        <>
            <Head title="Cari tempat printing" />
            <main className="flex flex-col gap-14 pb-4">
                <HomeHero shops={shops} openCount={openCount} />

                <section
                    id="daftar"
                    aria-labelledby="daftar-judul"
                    className="flex scroll-mt-20 flex-col gap-4 px-4"
                >
                    <div className="flex flex-col gap-1">
                        <h2
                            id="daftar-judul"
                            className="font-display text-2xl font-bold tracking-tight sm:text-3xl"
                        >
                            Semua tempat printing
                        </h2>
                        <p className="text-sm text-muted-foreground">
                            Data tempat masih contoh untuk pengembangan. Nama,
                            harga, dan jam buka akan diganti dengan hasil survei
                            tim.
                        </p>
                    </div>

                    {shops.length === 0 ? (
                        <EmptyState
                            title="Belum ada tempat printing"
                            hint="Data sedang disiapkan oleh tim survei."
                        />
                    ) : (
                        <>
                            <section className="flex flex-col gap-2">
                                <div
                                    role="group"
                                    aria-label="Urutkan dan filter"
                                    className="flex flex-wrap gap-2"
                                >
                                    {(
                                        Object.keys(sortLabels) as ShopSort[]
                                    ).map((option) => (
                                        <Button
                                            key={option}
                                            size="sm"
                                            variant={
                                                sort === option
                                                    ? 'default'
                                                    : 'outline'
                                            }
                                            aria-pressed={sort === option}
                                            disabled={
                                                option === 'distance' &&
                                                !hasDistance
                                            }
                                            onClick={() =>
                                                setChosenSort(option)
                                            }
                                        >
                                            {sortLabels[option]}
                                        </Button>
                                    ))}
                                    <Button
                                        size="sm"
                                        variant={
                                            openOnly ? 'default' : 'outline'
                                        }
                                        aria-pressed={openOnly}
                                        onClick={() => setOpenOnly((on) => !on)}
                                    >
                                        Masih buka
                                    </Button>
                                </div>
                                {!hasDistance && (
                                    <p className="text-xs text-muted-foreground">
                                        Izinkan lokasi untuk mengurutkan dari
                                        yang terdekat.
                                    </p>
                                )}
                            </section>

                            <div className="grid min-w-0 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                                {visibleShops.length === 0 ? (
                                    <EmptyState
                                        title="Tidak ada yang cocok"
                                        hint="Coba matikan filter Masih buka."
                                    />
                                ) : (
                                    <ShopList
                                        shops={visibleShops}
                                        selectedId={selectedId}
                                        onSelect={select}
                                        sorting={sorting}
                                    />
                                )}
                                <div className="lg:sticky lg:top-20">
                                    <ShopMap
                                        shops={shops}
                                        selectedId={selectedId}
                                        onSelect={select}
                                    />
                                </div>
                            </div>
                        </>
                    )}
                </section>

                <div className="flex flex-col gap-14 px-4">
                    <HowItWorks />
                    <AssistantBanner />
                </div>
            </main>
        </>
    );
}
