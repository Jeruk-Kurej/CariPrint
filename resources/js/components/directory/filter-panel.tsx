import { ChipRow } from '@/components/chip-row';
import { FilterChip } from '@/components/filter-chip';
import { SearchField } from '@/components/search-field';
import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';
import type { DirectoryFilters } from '@/hooks/use-directory-filters';
import {
    distanceOptionsKm,
    serviceGroups,
    sortLabels,
} from '@/lib/shop-filter';
import type { ServiceGroup, ShopSort } from '@/lib/shop-filter';

type Props = {
    filters: DirectoryFilters;
    hasDistance: boolean;
    locationStatus: 'loading' | 'granted' | 'denied';
};

/** Pencarian, sakelar buka, urutan, layanan, dan batas jarak untuk daftar tempat. */
export function FilterPanel({ filters, hasDistance, locationStatus }: Props) {
    const sortOptions = (Object.keys(sortLabels) as ShopSort[]).map(
        (value) => ({
            value,
            label: sortLabels[value],
            disabled: value === 'distance' && !hasDistance,
        }),
    );

    return (
        <section
            aria-label="Pencarian dan filter tempat"
            className="flex flex-col gap-2.5 paper-card p-3 sm:gap-3 sm:p-4"
        >
            <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <SearchField
                    value={filters.query}
                    onChange={filters.setQuery}
                />
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
            <ChipRow label="Layanan yang tersedia">
                {(Object.keys(serviceGroups) as ServiceGroup[]).map((group) => (
                    <FilterChip
                        key={group}
                        active={filters.services.includes(group)}
                        onClick={() => filters.toggleService(group)}
                    >
                        {serviceGroups[group].label}
                    </FilterChip>
                ))}
            </ChipRow>
            {hasDistance && (
                <ChipRow label="Batas jarak">
                    <span className="shrink-0 text-sm font-semibold">
                        Jarak
                    </span>
                    {distanceOptionsKm.map((km) => (
                        <FilterChip
                            key={km}
                            active={filters.maxKm === km}
                            onClick={() =>
                                filters.setMaxKm(
                                    filters.maxKm === km ? null : km,
                                )
                            }
                        >
                            {`≤ ${km} km`}
                        </FilterChip>
                    ))}
                </ChipRow>
            )}
            {locationStatus !== 'granted' && (
                <p aria-live="polite" className="text-xs text-muted-foreground">
                    {locationStatus === 'loading'
                        ? 'Mencari lokasimu...'
                        : 'Lokasi mati, jadi urut nama. Izinkan lokasi untuk urutan terdekat dan filter jarak.'}
                </p>
            )}
        </section>
    );
}
