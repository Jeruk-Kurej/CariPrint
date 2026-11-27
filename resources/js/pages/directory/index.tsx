import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { EmptyState } from '@/components/directory/empty-state';
import type { AiQuota } from '@/components/directory/ai-search';
import { FilterPanel } from '@/components/directory/filter-panel';
import { ShopList } from '@/components/directory/shop-list';
import { ShopMap } from '@/components/directory/shop-map';
import { PageTitle } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { useDirectoryFilters } from '@/hooks/use-directory-filters';
import { useGeolocation } from '@/hooks/use-geolocation';
import { useSelectedShop } from '@/hooks/use-selected-shop';
import { priceFor } from '@/lib/price-choice';
import { offeredServiceGroups } from '@/lib/shop-filter';
import { nearby } from '@/routes/directory';
import { create as suggest } from '@/routes/suggestions';
import type { ShopSummary } from '@/types';

type Props = { shops: ShopSummary[]; quota: AiQuota };

export default function DirectoryIndex({ shops, quota }: Props) {
    const location = useGeolocation();
    const { selectedId, select } = useSelectedShop();
    const [sorting, setSorting] = useState(false);

    const hasDistance = location.status === 'granted';
    const filters = useDirectoryFilters(shops, hasDistance);

    function showOnMap(id: number) {
        select(id);

        if (window.matchMedia('(min-width: 1024px)').matches) {
            return;
        }

        const reduceMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        ).matches;

        document.getElementById('peta-tempat')?.scrollIntoView({
            behavior: reduceMotion ? 'auto' : 'smooth',
            block: 'center',
        });
    }

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
                    <PageTitle>Cari tempat printing</PageTitle>
                    <p className="text-muted-foreground">
                        <span className="font-display font-bold text-foreground tabular-nums">
                            {filters.openCount}
                        </span>{' '}
                        dari {shops.length} tempat print sedang buka sekarang.
                    </p>
                </header>

                {shops.length === 0 ? (
                    <EmptyState
                        title="Belum ada tempat printing"
                        hint="Tim survei masih menyiapkan datanya."
                    />
                ) : (
                    <>
                        <FilterPanel
                            filters={filters}
                            quota={quota}
                            offeredServices={offeredServiceGroups(shops)}
                            hasDistance={hasDistance}
                            pricedCount={
                                filters.visibleShops.filter(
                                    (shop) =>
                                        priceFor(shop, filters.priceChoice) !==
                                        null,
                                ).length
                            }
                            locationStatus={location.status}
                        />

                        <div className="grid min-w-0 items-start gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
                            {filters.visibleShops.length === 0 ? (
                                <EmptyState
                                    title="Tidak ada yang cocok"
                                    hint="Coba kata kunci lain atau kurangi filternya."
                                >
                                    <Link
                                        href={suggest().url}
                                        className="mt-2 text-sm font-semibold text-primary hover:underline"
                                    >
                                        Tempatnya belum ada? Usulkan ke kami
                                    </Link>
                                </EmptyState>
                            ) : (
                                <ShopList
                                    shops={filters.visibleShops}
                                    priceChoice={filters.priceChoice}
                                    selectedId={selectedId}
                                    onSelect={showOnMap}
                                    sorting={sorting}
                                />
                            )}
                            <div
                                id="peta-tempat"
                                className="lg:sticky lg:top-20"
                            >
                                <ShopMap
                                    shops={shops}
                                    selectedId={selectedId}
                                    onSelect={select}
                                />
                            </div>
                        </div>

                        <aside className="relative flex flex-col items-start gap-2 rounded-sm bg-note-mint p-5 paper-shadow sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 className="font-display text-lg font-extrabold">
                                    Tempatnya belum ada?
                                </h2>
                                <p className="text-sm text-foreground/80">
                                    Kirim datanya, kami cek lalu tambahkan ke
                                    daftar.
                                </p>
                            </div>
                            <Button asChild className="min-h-11">
                                <Link href={suggest().url}>Usulkan tempat</Link>
                            </Button>
                        </aside>
                    </>
                )}
            </main>
        </>
    );
}
