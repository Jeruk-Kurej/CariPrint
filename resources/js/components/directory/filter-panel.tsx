import { ChipRow } from '@/components/chip-row';
import { AiSearch } from '@/components/directory/ai-search';
import type { AiQuota } from '@/components/directory/ai-search';
import { PriceChoiceFields } from '@/components/directory/price-choice-fields';
import { FilterChip } from '@/components/filter-chip';
import { SearchField } from '@/components/search-field';
import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';
import type { DirectoryFilters } from '@/hooks/use-directory-filters';
import { noChoices } from '@/lib/directory-url';
import { choiceLabel } from '@/lib/price-choice';
import {
    distanceOptionsKm,
    serviceGroups,
    sortLabels,
} from '@/lib/shop-filter';
import type { ServiceGroup, ShopSort } from '@/lib/shop-filter';

type Props = {
    filters: DirectoryFilters;
    quota: AiQuota;
    /** Kelompok layanan yang disediakan sedikitnya satu tempat. Yang belum ada tempatnya tidak ditampilkan. */
    offeredServices: ServiceGroup[];
    hasDistance: boolean;
    /** Jumlah tempat di daftar yang punya harga untuk kertas dan warna terpilih. */
    pricedCount: number;
    locationStatus: 'loading' | 'granted' | 'denied';
};

/** Pencarian, sakelar buka, urutan, kertas dan warna, layanan, dan batas jarak untuk daftar tempat. */
export function FilterPanel({
    filters,
    quota,
    offeredServices,
    hasDistance,
    pricedCount,
    locationStatus,
}: Props) {
    const choice = choiceLabel(filters.priceChoice);
    /** Layanan yang sedang dipilih tetap tampil walau belum ada tempatnya, supaya bisa dilepas. */
    const serviceChips = (Object.keys(serviceGroups) as ServiceGroup[]).filter(
        (group) =>
            offeredServices.includes(group) || filters.services.includes(group),
    );

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
                <div className="flex flex-1 flex-col gap-2 lg:max-w-md">
                    <SearchField
                        value={filters.query}
                        onChange={(query) => filters.update({ query })}
                    />
                    <AiSearch
                        sentence={filters.query}
                        quota={quota}
                        hasDistance={hasDistance}
                        onApply={filters.replace}
                        onReset={() => filters.replace(noChoices)}
                    />
                </div>
                <div className="flex flex-wrap items-center gap-4">
                    <SwitchField
                        label="Masih buka saja"
                        checked={filters.openOnly}
                        onChange={(openOnly) => filters.update({ openOnly })}
                    />
                    <SegmentedControl
                        label="Urutkan berdasarkan"
                        value={filters.sort}
                        options={sortOptions}
                        onChange={(sort) => filters.update({ sort })}
                    />
                </div>
            </div>
            <PriceChoiceFields
                value={filters.priceChoice}
                onChange={filters.update}
            />
            {(choice !== '' || filters.sort === 'price') && (
                <p aria-live="polite" className="text-xs text-muted-foreground">
                    {pricedCount} dari {filters.visibleShops.length} tempat di
                    daftar punya harga print{choice && ` ${choice}`}. Yang belum
                    punya tetap tampil
                    {filters.sort === 'price' && ' di urutan bawah'}.
                </p>
            )}
            {(serviceChips.length > 1 || filters.services.length > 0) && (
                <ChipRow label="Layanan yang tersedia">
                    {serviceChips.map((group) => (
                        <FilterChip
                            key={group}
                            active={filters.services.includes(group)}
                            onClick={() => filters.toggleService(group)}
                        >
                            {serviceGroups[group].label}
                        </FilterChip>
                    ))}
                </ChipRow>
            )}
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
                                filters.update({
                                    maxKm: filters.maxKm === km ? null : km,
                                })
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
