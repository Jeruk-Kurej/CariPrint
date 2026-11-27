import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { CompareTable } from '@/components/compare/compare-table';
import type {
    CompareRow,
    CompareShop,
} from '@/components/compare/compare-table';
import { EmptyState } from '@/components/directory/empty-state';
import { PageTitle } from '@/components/page-header';
import { SearchField } from '@/components/search-field';
import { ChipRow } from '@/components/chip-row';
import { FilterChip } from '@/components/filter-chip';
import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';
import { matchesShopQuery } from '@/lib/shop-search';
import { index as directory } from '@/routes/directory';

type ServiceFilter = 'all' | 'print-a4' | 'print-f4' | 'print-a3' | 'other';
type ColorFilter = 'all' | 'bw' | 'color';

const serviceFilters: { id: ServiceFilter; label: string }[] = [
    { id: 'all', label: 'Semua layanan' },
    { id: 'print-a4', label: 'Print A4' },
    { id: 'print-f4', label: 'Print F4' },
    { id: 'print-a3', label: 'Print A3' },
    { id: 'other', label: 'Fotokopi & lainnya' },
];

const colorOptions: { value: ColorFilter; label: string }[] = [
    { value: 'all', label: 'Semua' },
    { value: 'bw', label: 'Hitam-putih' },
    { value: 'color', label: 'Warna' },
];

function matchesService(row: CompareRow, filter: ServiceFilter): boolean {
    if (filter === 'all') {
        return true;
    }

    if (filter === 'other') {
        return row.service !== 'Print';
    }

    const size = filter.replace('print-', '');

    return row.service === 'Print' && row.paperSize === size;
}

type Props = {
    shops: CompareShop[];
    rows: CompareRow[];
    withoutPrices: number;
};

export default function CompareIndex({ shops, rows, withoutPrices }: Props) {
    const [serviceFilter, setServiceFilter] = useState<ServiceFilter>('all');
    const [colorFilter, setColorFilter] = useState<ColorFilter>('all');
    const [onlyOpen, setOnlyOpen] = useState(false);
    const [query, setQuery] = useState('');

    const visibleShopIndices = shops
        .map((shop, index) => ({ shop, index }))
        .filter(({ shop }) => !onlyOpen || shop.openState !== 'closed')
        .filter(({ shop }) => matchesShopQuery(shop, query))
        .map(({ index }) => index);

    const visibleRows = rows.filter(
        (row) =>
            matchesService(row, serviceFilter) &&
            (colorFilter === 'all' || row.colorMode === colorFilter),
    );

    return (
        <>
            <Head title="Bandingkan harga" />
            <main className="flex w-full flex-col gap-6 px-4 py-8 sm:py-12">
                <header className="flex flex-col gap-2">
                    <PageTitle>Bandingkan harga</PageTitle>
                    <p className="max-w-2xl text-muted-foreground">
                        Harga termurah di tiap layanan diberi warna kuning.
                    </p>
                    {withoutPrices > 0 && (
                        <p className="max-w-2xl text-sm text-muted-foreground">
                            {withoutPrices} tempat print lain belum punya daftar
                            harga, jadi tidak ikut dibandingkan. Lihat semuanya
                            di{' '}
                            <Link
                                href={directory().url}
                                className="-my-3 inline-block py-3 font-medium text-foreground underline"
                            >
                                Cari tempat
                            </Link>
                            .
                        </p>
                    )}
                </header>

                {rows.length === 0 ? (
                    <EmptyState
                        title="Belum ada harga untuk dibandingkan"
                        hint="Tim survei masih menyiapkan datanya."
                    />
                ) : (
                    <>
                        <section
                            aria-label="Filter perbandingan harga"
                            className="flex flex-col gap-3 paper-card p-4"
                        >
                            <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <SearchField
                                    value={query}
                                    onChange={setQuery}
                                />
                                <div className="flex flex-wrap items-center gap-4">
                                    <SwitchField
                                        label="Masih buka saja"
                                        checked={onlyOpen}
                                        onChange={setOnlyOpen}
                                    />
                                    <SegmentedControl
                                        label="Jenis warna"
                                        value={colorFilter}
                                        options={colorOptions}
                                        onChange={setColorFilter}
                                    />
                                </div>
                            </div>

                            <div className="border-t pt-3">
                                <ChipRow label="Layanan">
                                    {serviceFilters.map((item) => (
                                        <FilterChip
                                            key={item.id}
                                            active={serviceFilter === item.id}
                                            onClick={() =>
                                                setServiceFilter(item.id)
                                            }
                                        >
                                            {item.label}
                                        </FilterChip>
                                    ))}
                                </ChipRow>
                            </div>
                        </section>

                        {visibleShopIndices.length === 0 ||
                        visibleRows.length === 0 ? (
                            <EmptyState
                                title="Tidak ada yang cocok dengan filter ini"
                                hint="Coba matikan salah satu filter atau pilih Semua layanan."
                            />
                        ) : (
                            <CompareTable
                                shops={shops}
                                shopIndices={visibleShopIndices}
                                rows={visibleRows}
                            />
                        )}

                        <p className="text-sm text-muted-foreground">
                            Harga di sini masih perkiraan, jadi bisa berbeda
                            saat kamu cek langsung. Sumber dan tanggal pembaruan
                            tiap harga ada di halaman tempatnya.
                        </p>
                    </>
                )}
            </main>
        </>
    );
}
