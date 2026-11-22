import { router } from '@inertiajs/react';
import { Minus, Plus } from 'lucide-react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FilterChip } from '@/components/filter-chip';
import { SegmentedControl } from '@/components/segmented-control';
import { Tag } from '@/components/tag';
import { formatNumber, formatPageRuns } from '@/lib/format';
import { recalculate } from '@/routes/agent';

type PageRun = { from: number; to: number };
type PaperSize = 'a4' | 'f4' | 'a3';
type Assumed = 'paperSize' | 'copies';

export type PrintSpec = {
    paperSize: string;
    bwPages: number;
    colorPages: number;
    bwRuns: PageRun[];
    colorRuns: PageRun[];
    copies: number;
    binding: string | null;
    assumed: Assumed[];
};

type Edit = Partial<Pick<PrintSpec, 'paperSize' | 'copies' | 'binding'>>;

const paperOptions: { value: PaperSize; label: string }[] = [
    { value: 'a4', label: 'A4' },
    { value: 'f4', label: 'F4' },
    { value: 'a3', label: 'A3' },
];

const bindingOptions = [
    { value: null, label: 'Tanpa jilid' },
    { value: 'spiral_binding', label: 'Spiral' },
    { value: 'tape_binding', label: 'Lakban' },
    { value: 'hardcover_binding', label: 'Hardcover' },
];

const MAX_COPIES = 100;

const stepButton =
    'inline-flex size-11 items-center justify-center rounded-full border-2 border-foreground/15 bg-card transition-colors hover:border-primary focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-40';

/** Jumlah halaman satu mode cetak. Kalau pengguna menunjuk halaman tertentu, nomornya ikut ditulis. */
function pagesLabel(count: number, mode: string, runs: PageRun[]): string {
    const where = runs.length > 0 ? ` (hal. ${formatPageRuns(runs)})` : '';

    return `${formatNumber(count)} halaman ${mode}${where}`;
}

/** Kalimat tentang bagian yang tidak disebut pengguna dan diisi asisten dengan nilai bawaan. */
function assumptionNote(spec: PrintSpec): string | null {
    const guesses = [
        spec.assumed.includes('paperSize') &&
            `kertas ${spec.paperSize.toUpperCase()}`,
        spec.assumed.includes('copies') && `${spec.copies} rangkap`,
    ].filter((guess): guess is string => Boolean(guess));

    return guesses.length === 0
        ? null
        : `Kami anggap ${guesses.join(' dan ')} karena tidak kamu sebut. Kalau salah, ubah di bawah.`;
}

function Row({ label, children }: { label: string; children: ReactNode }) {
    return (
        <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
            <p className="text-sm font-semibold sm:w-20">{label}</p>
            {children}
        </div>
    );
}

type Props = {
    spec: PrintSpec;
    /** Dikirim sekali untuk menghitung jarak dan tidak disimpan. */
    coordinates: { latitude: number; longitude: number } | null;
};

/**
 * Hasil bacaan asisten sebagai slip pesanan. Kertas, rangkap, dan jilid bisa diubah, lalu estimasi
 * dihitung ulang dari daftar harga tanpa memanggil AI, jadi tidak memakai kuota. Jumlah halaman
 * ikut tulisan permintaan.
 */
export function SpecSlip({ spec, coordinates }: Props) {
    const [busy, setBusy] = useState(false);
    const note = assumptionNote(spec);

    function change(edit: Edit) {
        router.post(
            recalculate().url,
            {
                paper_size: edit.paperSize ?? spec.paperSize,
                copies: edit.copies ?? spec.copies,
                binding: 'binding' in edit ? edit.binding : spec.binding,
                bw_pages: spec.bwPages,
                color_pages: spec.colorPages,
                bw_runs: spec.bwRuns,
                color_runs: spec.colorRuns,
                assumed: spec.assumed.filter((item) => !(item in edit)),
                latitude: coordinates?.latitude ?? null,
                longitude: coordinates?.longitude ?? null,
            },
            {
                preserveScroll: true,
                preserveState: true,
                only: ['spec', 'estimates', 'shopsWithoutPrices'],
                onStart: () => setBusy(true),
                onFinish: () => setBusy(false),
            },
        );
    }

    return (
        <div className="flex flex-col gap-4 paper-card p-4">
            <Row label="Kertas">
                <SegmentedControl
                    label="Ukuran kertas"
                    value={spec.paperSize as PaperSize}
                    options={paperOptions.map((option) => ({
                        ...option,
                        disabled: busy,
                    }))}
                    onChange={(paperSize) => change({ paperSize })}
                />
            </Row>
            <Row label="Rangkap">
                <div className="flex items-center gap-3">
                    <button
                        type="button"
                        aria-label="Kurangi rangkap"
                        disabled={busy || spec.copies <= 1}
                        onClick={() => change({ copies: spec.copies - 1 })}
                        className={stepButton}
                    >
                        <Minus className="size-4" aria-hidden />
                    </button>
                    <span
                        aria-live="polite"
                        className="min-w-8 text-center font-display text-xl font-bold tabular-nums"
                    >
                        {spec.copies}
                    </span>
                    <button
                        type="button"
                        aria-label="Tambah rangkap"
                        disabled={busy || spec.copies >= MAX_COPIES}
                        onClick={() => change({ copies: spec.copies + 1 })}
                        className={stepButton}
                    >
                        <Plus className="size-4" aria-hidden />
                    </button>
                </div>
            </Row>
            <Row label="Jilid">
                <div
                    role="group"
                    aria-label="Jenis jilid"
                    className="flex flex-wrap gap-2"
                >
                    {bindingOptions.map((option) => (
                        <FilterChip
                            key={option.label}
                            active={spec.binding === option.value}
                            disabled={busy}
                            onClick={() => change({ binding: option.value })}
                        >
                            {option.label}
                        </FilterChip>
                    ))}
                </div>
            </Row>
            <Row label="Halaman">
                <ul className="flex flex-wrap gap-2">
                    {spec.bwPages > 0 && (
                        <li>
                            <Tag>
                                {pagesLabel(
                                    spec.bwPages,
                                    'hitam-putih',
                                    spec.bwRuns,
                                )}
                            </Tag>
                        </li>
                    )}
                    {spec.colorPages > 0 && (
                        <li>
                            <Tag>
                                {pagesLabel(
                                    spec.colorPages,
                                    'warna',
                                    spec.colorRuns,
                                )}
                            </Tag>
                        </li>
                    )}
                </ul>
            </Row>
            <p className="text-sm text-muted-foreground">
                {note ?? 'Estimasi dihitung ulang setiap kamu mengubah slip.'}{' '}
                Jumlah halaman mengikuti tulisan permintaanmu.
            </p>
        </div>
    );
}
