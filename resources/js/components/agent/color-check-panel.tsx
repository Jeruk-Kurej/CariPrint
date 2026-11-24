import { Button } from '@/components/ui/button';
import type { PdfAttachmentState } from '@/hooks/use-pdf-attachment';
import { formatNumber, formatPageNumbers, formatRupiah } from '@/lib/format';

/**
 * Hasil cek warna file: halaman mana yang ada warnanya, mana yang mungkin cukup hitam-putih, dan berapa
 * hematnya. Server yang menilai dan menghitung hematnya. Pengguna memilih sendiri tiap halaman.
 */
export function ColorCheckPanel({ pdf }: { pdf: PdfAttachmentState }) {
    const { file } = pdf;

    if (file.status !== 'ready') {
        return null;
    }

    if (file.needs === null) {
        return (
            <p
                aria-live="polite"
                className="border-t pt-2.5 text-xs text-muted-foreground"
            >
                Menilai warna tiap halaman
            </p>
        );
    }

    const colored = file.needs
        .map((need, index) => ({ page: index + 1, need }))
        .filter((item) => item.need !== 'bw');

    if (colored.length === 0) {
        return (
            <div className="border-t pt-2.5">
                <span className="rounded-md bg-secondary px-2.5 py-1 text-xs font-medium">
                    Semua {formatNumber(file.pages)} halaman hitam-putih
                </span>
            </div>
        );
    }

    const clear = colored.filter((item) => item.need === 'color').length;
    const maybe = colored.length - clear;
    const savings = file.savings;

    return (
        <div className="flex flex-col gap-3 border-t pt-2.5">
            <p>
                <span className="font-semibold">
                    {formatNumber(colored.length)} halaman ada warnanya.
                </span>{' '}
                {clear > 0 && `${formatNumber(clear)} jelas perlu warna`}
                {clear > 0 && maybe > 0 && ', '}
                {maybe > 0 &&
                    `${formatNumber(maybe)} mungkin cukup hitam-putih, misalnya karena hanya judul atau logo yang berwarna`}
                .
            </p>

            <div className="flex flex-col gap-1.5">
                <p className="text-xs text-muted-foreground">
                    Ketuk nomor halaman untuk memilih warna atau hitam-putih.
                    Garis putus-putus berarti mungkin cukup hitam-putih.
                </p>
                <ul className="flex max-h-44 flex-wrap gap-1.5 overflow-y-auto">
                    {colored.map(({ page, need }) => {
                        const inColor = file.chosen.includes(page);

                        return (
                            <li key={page}>
                                <button
                                    type="button"
                                    aria-pressed={inColor}
                                    aria-label={`Halaman ${page}, ${inColor ? 'warna' : 'hitam-putih'}${need === 'maybe' ? ', mungkin cukup hitam-putih' : ''}`}
                                    onClick={() => pdf.toggle(page)}
                                    className={`inline-flex min-h-11 min-w-11 items-center justify-center rounded-full border-2 px-3 text-sm font-semibold tabular-nums transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none ${need === 'maybe' ? 'border-dashed' : ''} ${inColor ? 'border-primary bg-primary text-primary-foreground' : 'border-foreground/25 bg-card'}`}
                                >
                                    {page}
                                </button>
                            </li>
                        );
                    })}
                </ul>
            </div>

            <div className="flex flex-wrap gap-2">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    className="min-h-11"
                    onClick={() => pdf.setChosen(file.suggested)}
                >
                    Ikuti saran
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    className="min-h-11"
                    onClick={() => pdf.setChosen([])}
                >
                    Semua hitam-putih
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    className="min-h-11"
                    onClick={() =>
                        pdf.setChosen(colored.map((item) => item.page))
                    }
                >
                    Semua warna
                </Button>
            </div>

            <p className="font-medium">
                {file.chosen.length === 0
                    ? 'Semua halaman dicetak hitam-putih.'
                    : `Berwarna: hal. ${formatPageNumbers(file.chosen)}. Sisanya hitam-putih.`}
            </p>

            {savings !== null && savings.saving > 0 && (
                <p className="rounded-md bg-note-mint px-3 py-2 font-semibold">
                    Hemat {formatRupiah(savings.saving)} di {savings.shop}{' '}
                    dibanding semua halaman berwarna dicetak dengan warna
                    (hitungan per salinan A4).
                </p>
            )}
        </div>
    );
}
