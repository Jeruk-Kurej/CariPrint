import { Tag } from '@/components/tag';
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
    assumed: ('paperSize' | 'copies')[];
};

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
        : `Kami anggap ${guesses.join(' dan ')} karena tidak kamu sebut. Kalau salah, tulis ulang permintaanmu dengan menyebutnya.`;
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

    const note = assumptionNote(spec);

    return (
        <div className="flex flex-col gap-2">
            <ul className="flex flex-wrap gap-2">
                {parts.map((part) => (
                    <li key={part}>
                        <Tag>{part}</Tag>
                    </li>
                ))}
            </ul>
            {note && <p className="text-sm text-muted-foreground">{note}</p>}
        </div>
    );
}
