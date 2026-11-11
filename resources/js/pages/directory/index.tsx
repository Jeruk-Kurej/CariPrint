import { Head } from '@inertiajs/react';
import { EmptyState } from '@/components/directory/empty-state';
import { FilterBar } from '@/components/directory/filter-bar';
import { ShopList } from '@/components/directory/shop-list';
import { ShopMap } from '@/components/directory/shop-map';
import { PageHeader } from '@/components/page-header';
import { useDirectoryFilters } from '@/hooks/use-directory-filters';
import { useNearbyShops } from '@/hooks/use-nearby-shops';
import { useSelectedShop } from '@/hooks/use-selected-shop';
import type { ShopSummary } from '@/types';

export default function DirectoryIndex({ shops }: { shops: ShopSummary[] }) {
    const { hasDistance, sorting } = useNearbyShops();
    const { selectedId, select } = useSelectedShop();
    const filters = useDirectoryFilters(shops, hasDistance);

    return (
        <>
            <Head title="Cari tempat printing" />
            <main className="flex flex-col gap-6 px-4 py-8 sm:py-12">
                <PageHeader title="Cari tempat printing">
                    <p className="text-muted-foreground">
                        <span className="font-display font-bold text-foreground tabular-nums">
                            {filters.openCount}
                        </span>{' '}
                        dari {shops.length} tempat masih buka sekarang.
                    </p>
                    <p className="text-sm text-muted-foreground">
                        Lokasi dan jam buka diambil dari Google Maps. Harga
                        ditambahkan setelah dikonfirmasi pemilik tempat.
                    </p>
                </PageHeader>

                {shops.length === 0 ? (
                    <EmptyState
                        title="Belum ada tempat printing"
                        hint="Data sedang disiapkan oleh tim survei."
                    />
                ) : (
                    <>
                        <FilterBar
                            filters={filters}
                            hasDistance={hasDistance}
                        />
                        <div className="grid min-w-0 items-start gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
                            {filters.visibleShops.length === 0 ? (
                                <EmptyState
                                    title="Tidak ada yang cocok"
                                    hint="Ubah kata pencarian atau matikan filter Masih buka."
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
