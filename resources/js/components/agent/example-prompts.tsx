import { ChipRow } from '@/components/chip-row';
import { FilterChip } from '@/components/filter-chip';

type Example = { label: string; text: string };

const textExamples: Example[] = [
    {
        label: 'Skripsi, jilid hardcover',
        text: 'Skripsi 80 halaman hitam putih A4, 2 rangkap, jilid hardcover',
    },
    { label: 'Fotokopi KTP', text: 'Fotokopi KTP 2 lembar' },
    { label: 'Poster A3 warna', text: 'Poster A3 warna 1 lembar' },
    {
        label: 'F4, jilid spiral',
        text: '25 halaman hitam putih F4, jilid spiral',
    },
];

const pdfExamples: Example[] = [
    {
        label: 'Semua hitam putih, spiral',
        text: 'Print file ini hitam putih, jilid spiral',
    },
    {
        label: 'Warna hanya yang perlu',
        text: 'Print semua, warna hanya yang perlu',
    },
    {
        label: 'Hal. 1-5 warna',
        text: 'Halaman 1-5 warna, sisanya hitam putih, 2 rangkap',
    },
    {
        label: 'Tanpa halaman terakhir',
        text: 'Semua kecuali halaman terakhir',
    },
];

type Props = {
    /** Contoh berganti ke yang memakai file kalau PDF sudah terlampir. */
    hasPdf: boolean;
    onPick: (text: string) => void;
};

/** Contoh permintaan yang mengisi kotak teks lewat satu ketukan. Label pendek supaya satu baris yang bisa digeser cukup di ponsel. */
export function ExamplePrompts({ hasPdf, onPick }: Props) {
    return (
        <div className="flex flex-col gap-1">
            <p className="text-sm font-semibold">Contoh, ketuk untuk mengisi</p>
            <ChipRow label="Contoh permintaan" fadeFrom="from-note-yellow">
                {(hasPdf ? pdfExamples : textExamples).map((example) => (
                    <FilterChip
                        key={example.label}
                        onClick={() => onPick(example.text)}
                    >
                        {example.label}
                    </FilterChip>
                ))}
            </ChipRow>
        </div>
    );
}
