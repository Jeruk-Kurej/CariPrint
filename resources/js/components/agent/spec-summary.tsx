import { formatNumber, formatPageRuns } from '@/lib/format';
import { serviceLabel } from '@/lib/labels';

type PageRun = { from: number; to: number };

export type PrintSpec = {
    paperSize: string;
    bwPages: number;
    colorPages: number;
    bwRuns: PageRun[];
    colorRuns: PageRun[];
    copies: number;
    binding: string | null;
};

/** Jumlah halaman satu mode cetak. Kalau pengguna menunjuk halaman tertentu, nomornya ikut ditulis. */
function pagesLabel(count: number, mode: string, runs: PageRun[]): string {
    const where = runs.length > 0 ? ` (hal. ${formatPageRuns(runs)})` : '';

    return `${formatNumber(count)} halaman ${mode}${where}`;
}

/** Hasil bacaan asisten, supaya pengguna bisa memeriksa sebelum percaya estimasinya. */
export function SpecSummary({ spec }: { spec: PrintSpec }) {
    const parts = [
        `Kertas ${spec.paperSize.toUpperCase()}`,
        spec.bwPages > 0 &&
            pagesLabel(spec.bwPages, 'hitam-putih', spec.bwRuns),
        spec.colorPages > 0 &&
            pagesLabel(spec.colorPages, 'warna', spec.colorRuns),
        `${spec.copies} rangkap`,
        spec.binding && serviceLabel(spec.binding),
    ].filter((part): part is string => Boolean(part));

    return (
        <ul className="flex flex-wrap gap-2">
            {parts.map((part) => (
                <li
                    key={part}
                    className="rounded-md bg-secondary px-2.5 py-1 text-sm font-medium"
                >
                    {part}
                </li>
            ))}
        </ul>
    );
}
