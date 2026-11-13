import { needsColor } from '@/lib/page-colour';

/** Lebar gambar kecil tiap halaman yang diperiksa warnanya. Cukup untuk menilai luas warna dan tetap cepat. */
const THUMBNAIL_WIDTH = 240;

export type PdfAnalysis = {
    pages: number;
    /** Nomor halaman (mulai dari 1) yang perlu dicetak berwarna. */
    coloredPages: number[];
};

/** PDF lebih panjang dari batas, jadi warnanya tidak diperiksa. */
export class PdfTooLongError extends Error {
    constructor(public readonly pages: number) {
        super(`PDF ${pages} halaman melebihi batas`);
    }
}

type AnalyzeOptions = {
    /** Batas jumlah halaman. Lebih dari itu langsung ditolak dengan PdfTooLongError. */
    maxPages: number;
    /** Dipanggil sesudah tiap halaman: (halaman selesai, jumlah halaman). */
    onProgress: (done: number, total: number) => void;
    /** Pemeriksaan dihentikan lebih awal kalau mengembalikan false, misalnya file diganti atau dihapus. */
    isCurrent: () => boolean;
};

/**
 * Membaca PDF di perangkat pengguna: jumlah halamannya dan halaman mana yang berwarna. Filenya tidak
 * dikirim ke mana pun, dan gambar halaman hanya dipakai sebentar untuk menghitung piksel.
 *
 * pdf.js baru dimuat saat fungsi ini dipanggil supaya halaman lain tetap ringan. Yang dipakai build
 * "legacy" karena build biasanya hanya berjalan di browser versi terbaru. Modul worker diimpor langsung
 * sehingga pdf.js bekerja di thread utama dan tidak bergantung pada dukungan worker di browser maupun
 * pengaturan server. Karena itu halaman diperiksa satu per satu dengan jeda, supaya layar tetap responsif.
 *
 * @returns null kalau dihentikan oleh `isCurrent`.
 */
export async function analyzePdf(
    file: File,
    { maxPages, onProgress, isCurrent }: AnalyzeOptions,
): Promise<PdfAnalysis | null> {
    const [pdfjs, data] = await Promise.all([
        import('pdfjs-dist/legacy/build/pdf.mjs'),
        file.arrayBuffer(),
        import('pdfjs-dist/legacy/build/pdf.worker.min.mjs'),
    ]);

    const loading = pdfjs.getDocument({ data });

    try {
        const pdf = await loading.promise;

        if (pdf.numPages > maxPages) {
            throw new PdfTooLongError(pdf.numPages);
        }

        const coloredPages: number[] = [];
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { willReadFrequently: true });

        if (context === null) {
            throw new Error('Canvas tidak tersedia');
        }

        onProgress(0, pdf.numPages);

        for (let number = 1; number <= pdf.numPages; number++) {
            if (!isCurrent()) {
                return null;
            }

            const page = await pdf.getPage(number);
            const natural = page.getViewport({ scale: 1 });
            const viewport = page.getViewport({
                scale: THUMBNAIL_WIDTH / natural.width,
            });

            canvas.width = Math.max(1, Math.floor(viewport.width));
            canvas.height = Math.max(1, Math.floor(viewport.height));

            await page.render({
                canvas,
                viewport,
                background: 'rgb(255, 255, 255)',
            }).promise;

            const pixels = context.getImageData(
                0,
                0,
                canvas.width,
                canvas.height,
            );

            if (needsColor(pixels.data)) {
                coloredPages.push(number);
            }

            page.cleanup();
            onProgress(number, pdf.numPages);

            // Beri browser kesempatan menggambar progres dan menerima klik.
            await new Promise((resolve) => setTimeout(resolve, 0));
        }

        return { pages: pdf.numPages, coloredPages };
    } finally {
        await loading.destroy();
    }
}
