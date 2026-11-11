import { serviceLabel } from '@/lib/labels';

export type PrintSpec = {
    paperSize: string;
    bwPages: number;
    colorPages: number;
    copies: number;
    binding: string | null;
};

/** Hasil bacaan asisten, supaya pengguna bisa memeriksa sebelum percaya estimasinya. */
export function SpecSummary({ spec }: { spec: PrintSpec }) {
    const parts = [
        `Kertas ${spec.paperSize.toUpperCase()}`,
        spec.bwPages > 0 && `${spec.bwPages} halaman hitam-putih`,
        spec.colorPages > 0 && `${spec.colorPages} halaman warna`,
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
