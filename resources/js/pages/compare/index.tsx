import { Head } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useState } from 'react';
import { CompareTable } from '@/components/compare/compare-table';
import type {
    CompareRow,
    CompareShop,
} from '@/components/compare/compare-table';
import { EmptyState } from '@/components/directory/empty-state';
import { FilterChip } from '@/components/filter-chip';
import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';

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
};

export default function CompareIndex({ shops, rows }: Props) {
    const [serviceFilter, setServiceFilter] = useState<ServiceFilter>('all');
    const [colorFilter, setColorFilter] = useState<ColorFilter>('all');
    const [onlyOpen, setOnlyOpen] = useState(false);
    const [query, setQuery] = useState('');

    const normalizedQuery = query.trim().toLowerCase();

    const visibleShopIndices = shops
        .map((shop, index) => ({ shop, index }))
        .filter(({ shop }) => !onlyOpen || shop.openState !== 'closed')
        .filter(
            ({ shop }) =>
                normalizedQuery === '' ||
                shop.name.toLowerCase().includes(normalizedQuery),
        )
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
                    <h1 className="font-display text-3xl leading-tight font-extrabold tracking-tight sm:text-5xl">
                        Bandingkan harga
                    </h1>
                    <p className="max-w-2xl text-muted-foreground">
                        Tiap baris adalah satu tempat printing dan tiap kolom
                        adalah layanan, sehingga daftar tetap rapi ke bawah
                        meski jumlah tempat bertambah. Harga paling murah di
                        tiap kolom diberi warna kuning.
                    </p>
                </header>

                {rows.length === 0 ? (
                    <EmptyState
                        title="Belum ada harga untuk dibandingkan"
                        hint="Data sedang disiapkan oleh tim survei."
                    />
                ) : (
                    <>
                        <section
                            aria-label="Filter perbandingan harga"
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
                                        value={query}
                                        onChange={(event) =>
                                            setQuery(event.target.value)
                                        }
                                        placeholder="Cari nama tempat..."
                                        aria-label="Cari nama tempat"
                                        className="w-full rounded-full border-2 border-foreground/15 bg-background py-1.5 pr-3 pl-9 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    />
                                </div>
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

                            <div className="flex flex-wrap items-center gap-2 border-t pt-3">
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
                            </div>
                        </section>

                        {visibleShopIndices.length === 0 ||
                        visibleRows.length === 0 ? (
                            <EmptyState
                                title="Tidak ada data yang cocok dengan filter ini"
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
                            Harga masih berupa perkiraan dan bisa berbeda saat
                            dicek langsung. Buka halaman tempatnya untuk melihat
                            sumber dan tanggal pembaruan tiap harga.
                        </p>
                    </>
                )}
            </main>
        </>
    );
}
