import { FilterChip } from '@/components/filter-chip';
import { Input } from '@/components/ui/input';
import type { DirectoryFilters } from '@/hooks/use-directory-filters';
import { sortLabels } from '@/lib/shop-filter';
import type { ShopSort } from '@/lib/shop-filter';

/** Kotak cari nama, pilihan urutan, dan filter "Masih buka". */
export function FilterBar({
    filters,
    hasDistance,
}: {
    filters: DirectoryFilters;
    hasDistance: boolean;
}) {
    return (
        <section className="flex flex-col gap-3">
            <Input
                type="search"
                value={filters.query}
                onChange={(event) => filters.setQuery(event.target.value)}
                placeholder="Cari nama tempat"
                aria-label="Cari nama tempat"
                className="max-w-sm bg-card"
            />
            <div
                role="group"
                aria-label="Urutkan dan filter"
                className="flex flex-wrap gap-2"
            >
                {(Object.keys(sortLabels) as ShopSort[]).map((option) => (
                    <FilterChip
                        key={option}
                        active={filters.sort === option}
                        disabled={option === 'distance' && !hasDistance}
                        onClick={() => filters.setSort(option)}
                    >
                        {sortLabels[option]}
                    </FilterChip>
                ))}
                <FilterChip
                    active={filters.openOnly}
                    onClick={filters.toggleOpenOnly}
                >
                    Masih buka
                </FilterChip>
            </div>
            {!hasDistance && (
                <p className="text-xs text-muted-foreground">
                    Izinkan lokasi untuk mengurutkan dari yang terdekat.
                </p>
            )}
        </section>
    );
}
