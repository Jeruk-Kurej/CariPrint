import { FilterChip } from '@/components/filter-chip';

const textExamples = [
    'Skripsi 80 halaman hitam putih A4, 2 rangkap, jilid hardcover',
    'Fotokopi KTP 2 lembar',
    'Poster A3 warna 1 lembar',
    '25 halaman hitam putih F4, jilid spiral',
];

const pdfExamples = [
    'Print file ini hitam putih, jilid spiral',
    'Print semua, warna hanya yang perlu',
    'Halaman 1-5 warna, sisanya hitam putih, 2 rangkap',
    'Semua kecuali halaman terakhir',
];

type Props = {
    /** Contoh berganti ke yang memakai file kalau PDF sudah terlampir. */
    hasPdf: boolean;
    onPick: (text: string) => void;
};

/** Contoh permintaan yang mengisi kotak teks lewat satu ketukan, supaya pengguna tahu apa saja yang bisa ditulis. */
export function ExamplePrompts({ hasPdf, onPick }: Props) {
    return (
        <div
            role="group"
            aria-label="Contoh permintaan"
            className="flex flex-col gap-2"
        >
            <p className="text-sm font-semibold">Contoh, ketuk untuk mengisi</p>
            <ul className="flex flex-wrap gap-2">
                {(hasPdf ? pdfExamples : textExamples).map((example) => (
                    <li key={example}>
                        <FilterChip onClick={() => onPick(example)}>
                            {example}
                        </FilterChip>
                    </li>
                ))}
            </ul>
        </div>
    );
}
