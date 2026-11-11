import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';
import { Input } from '@/components/ui/input';
import type { DirectoryFilters } from '@/hooks/use-directory-filters';
import { sortLabels } from '@/lib/shop-filter';
import type { ShopSort } from '@/lib/shop-filter';

/** Kotak cari nama, pilihan urutan (pilih satu), dan sakelar "hanya yang masih buka" (ya atau tidak). */
export function FilterBar({
    filters,
    hasDistance,
}: {
    filters: DirectoryFilters;
    hasDistance: boolean;
}) {
    const sortOptions = (Object.keys(sortLabels) as ShopSort[]).map(
        (value) => ({
            value,
            label: sortLabels[value],
            disabled: value === 'distance' && !hasDistance,
        }),
    );

    return (
        <section className="flex flex-col gap-4">
            <Input
                type="search"
                value={filters.query}
                onChange={(event) => filters.setQuery(event.target.value)}
                placeholder="Cari nama tempat"
                aria-label="Cari nama tempat"
                className="max-w-sm bg-card"
            />
            <div className="flex flex-wrap items-center gap-x-6 gap-y-3">
                <SegmentedControl
                    label="Urutkan"
                    value={filters.sort}
                    options={sortOptions}
                    onChange={filters.setSort}
                />
                <SwitchField
                    label="Hanya yang masih buka"
                    checked={filters.openOnly}
                    onChange={filters.setOpenOnly}
                />
            </div>
            {!hasDistance && (
                <p className="text-xs text-muted-foreground">
                    Izinkan lokasi untuk mengurutkan dari yang terdekat.
                </p>
            )}
        </section>
    );
}
