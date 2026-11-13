import { Head, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useEffect, useState } from 'react';
import { EmptyState } from '@/components/directory/empty-state';
import { ShopList } from '@/components/directory/shop-list';
import { ShopMap } from '@/components/directory/shop-map';
import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';
import { useDirectoryFilters } from '@/hooks/use-directory-filters';
import { useGeolocation } from '@/hooks/use-geolocation';
import { useSelectedShop } from '@/hooks/use-selected-shop';
import type { ShopSort } from '@/lib/shop-filter';
import { nearby } from '@/routes/directory';
import type { ShopSummary } from '@/types';

export default function DirectoryIndex({ shops }: { shops: ShopSummary[] }) {
    const location = useGeolocation();
    const { selectedId, select } = useSelectedShop();
    const [sorting, setSorting] = useState(false);

    const hasDistance = location.status === 'granted';
    const filters = useDirectoryFilters(shops, hasDistance);

    const sortOptions: {
        value: ShopSort;
        label: string;
        disabled?: boolean;
    }[] = [
        { value: 'distance', label: 'Terdekat', disabled: !hasDistance },
        { value: 'price', label: 'Termurah' },
        { value: 'name', label: 'Nama' },
    ];

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
    }, [location, hasDistance]);

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
                            {filters.openCount}
                        </span>{' '}
                        dari {shops.length} tempat masih buka sekarang.
                    </p>
                </header>

                {shops.length === 0 ? (
                    <EmptyState
                        title="Belum ada tempat printing"
                        hint="Data sedang disiapkan oleh tim survei."
                    />
                ) : (
                    <>
                        <section
                            aria-label="Pencarian dan filter tempat"
                            className="flex flex-col gap-3 rounded-lg border bg-card p-4"
                        >
                            <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div className="relative flex-1 sm:max-w-xs">
                                    <Search
                                        aria-hidden
                                        className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                    />
                                    <input
                                        type="search"
                                        value={filters.query}
                                        onChange={(event) =>
                                            filters.setQuery(event.target.value)
                                        }
                                        placeholder="Cari nama tempat..."
                                        aria-label="Cari nama tempat"
                                        className="w-full rounded-full border-2 border-foreground/15 bg-background py-1.5 pr-3 pl-9 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    />
                                </div>
                                <div className="flex flex-wrap items-center gap-4">
                                    <SwitchField
                                        label="Masih buka saja"
                                        checked={filters.openOnly}
                                        onChange={filters.setOpenOnly}
                                    />
                                    <SegmentedControl
                                        label="Urutkan berdasarkan"
                                        value={filters.sort}
                                        options={sortOptions}
                                        onChange={filters.setSort}
                                    />
                                </div>
                            </div>
                            {!hasDistance && (
                                <p className="text-xs text-muted-foreground">
                                    Izinkan lokasi di browser untuk mengurutkan
                                    dari yang terdekat.
                                </p>
                            )}
                        </section>

                        <div className="grid min-w-0 items-start gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
                            {filters.visibleShops.length === 0 ? (
                                <EmptyState
                                    title="Tidak ada yang cocok"
                                    hint="Ubah kata pencarian atau matikan sakelar Masih buka saja."
                                />
                            ) : (
                                <ShopList
                                    shops={filters.visibleShops}
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
