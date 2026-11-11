import { Head, router } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { EmptyState } from '@/components/directory/empty-state';
import { ShopList } from '@/components/directory/shop-list';
import { ShopMap } from '@/components/directory/shop-map';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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

/** Pencarian, urutan, dan filter bisa dibawa lewat alamat, misalnya dari beranda. */
function initialParams(): URLSearchParams {
    return new URLSearchParams(window.location.search);
}

function parseSort(value: string | null): ShopSort | null {
    return value !== null && value in sortLabels ? (value as ShopSort) : null;
}

export default function DirectoryIndex({ shops }: { shops: ShopSummary[] }) {
    const location = useGeolocation();
    const { selectedId, select } = useSelectedShop();
    const [sorting, setSorting] = useState(false);
    const [openOnly, setOpenOnly] = useState(
        () => initialParams().get('buka') === '1',
    );
    const [query, setQuery] = useState(() => initialParams().get('q') ?? '');
    const [chosenSort, setChosenSort] = useState<ShopSort | null>(() =>
        parseSort(initialParams().get('urut')),
    );

    const hasDistance = location.status === 'granted';
    const wanted = chosenSort ?? 'distance';
    const sort = wanted === 'distance' && !hasDistance ? 'name' : wanted;

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
        () => filterAndSortShops(shops, { openOnly, sort, query }),
        [shops, openOnly, sort, query],
    );
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    return (
        <>
            <Head title="Cari tempat printing" />
            <main className="flex flex-col gap-6 px-4 py-8 sm:py-12">
                <header className="flex flex-col gap-2">
                    <h1 className="font-display text-3xl leading-tight font-extrabold tracking-tight sm:text-5xl">
                        Cari tempat printing
                    </h1>
                    <p className="text-muted-foreground">
                        <span className="font-display font-bold text-foreground tabular-nums">
                            {openCount}
                        </span>{' '}
                        dari {shops.length} tempat masih buka sekarang.
                    </p>
                    <p className="text-sm text-muted-foreground">
                        Data tempat masih contoh untuk pengembangan. Nama,
                        harga, dan jam buka akan diganti dengan hasil survei
                        tim.
                    </p>
                </header>

                {shops.length === 0 ? (
                    <EmptyState
                        title="Belum ada tempat printing"
                        hint="Data sedang disiapkan oleh tim survei."
                    />
                ) : (
                    <>
                        <section className="flex flex-col gap-3">
                            <Input
                                type="search"
                                value={query}
                                onChange={(event) =>
                                    setQuery(event.target.value)
                                }
                                placeholder="Cari nama tempat"
                                aria-label="Cari nama tempat"
                                className="max-w-sm bg-card"
                            />
                            <div
                                role="group"
                                aria-label="Urutkan dan filter"
                                className="flex flex-wrap gap-2"
                            >
                                {(Object.keys(sortLabels) as ShopSort[]).map(
                                    (option) => (
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
                                    ),
                                )}
                                <Button
                                    size="sm"
                                    variant={openOnly ? 'default' : 'outline'}
                                    aria-pressed={openOnly}
                                    onClick={() => setOpenOnly((on) => !on)}
                                >
                                    Masih buka
                                </Button>
                            </div>
                            {!hasDistance && (
                                <p className="text-xs text-muted-foreground">
                                    Izinkan lokasi untuk mengurutkan dari yang
                                    terdekat.
                                </p>
                            )}
                        </section>

                        <div className="grid min-w-0 items-start gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
                            {visibleShops.length === 0 ? (
                                <EmptyState
                                    title="Tidak ada yang cocok"
                                    hint="Ubah kata pencarian atau matikan filter Masih buka."
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
            </main>
        </>
    );
}
