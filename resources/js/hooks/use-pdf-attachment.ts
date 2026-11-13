import type { ChangeEvent } from 'react';
import { useRef, useState } from 'react';
import { formatNumber } from '@/lib/format';
import { PdfTooLongError, analyzePdf } from '@/lib/pdf';

export type FileLimits = {
    maxMegabytes: number;
    maxPages: number;
};

export type AttachedPdf =
    | { status: 'empty' }
    | { status: 'reading'; name: string; done: number; total: number | null }
    | { status: 'ready'; name: string; pages: number; coloredPages: number[] }
    | { status: 'rejected'; message: string };

const UNREADABLE_MESSAGE =
    'File ini tidak bisa dibaca. Pastikan filenya PDF yang tidak rusak dan tidak dikunci kata sandi.';

function isPdf(file: File): boolean {
    return file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
}

/**
 * Lampiran PDF untuk asisten cetak. File dibaca di perangkat pengguna untuk menghitung halamannya dan
 * memeriksa halaman mana yang berwarna, dan tidak pernah dikirim ke server. Yang dikirim nanti cuma
 * jumlah halaman dan nomor halaman yang berwarna.
 * onChange dipanggil setiap kali pengguna memilih atau menghapus file.
 */
export function usePdfAttachment(limits: FileLimits, onChange: () => void) {
    const [file, setFile] = useState<AttachedPdf>({ status: 'empty' });
    const input = useRef<HTMLInputElement>(null);
    const latestPick = useRef(0);

    async function attach(picked: File) {
        const pick = ++latestPick.current;
        const isCurrent = () => pick === latestPick.current;

        onChange();

        if (!isPdf(picked)) {
            setFile({ status: 'rejected', message: 'File harus PDF.' });

            return;
        }

        if (picked.size > limits.maxMegabytes * 1024 * 1024) {
            setFile({
                status: 'rejected',
                message: `Ukuran file maksimal ${limits.maxMegabytes} MB.`,
            });

            return;
        }

        setFile({
            status: 'reading',
            name: picked.name,
            done: 0,
            total: null,
        });

        try {
            const analysis = await analyzePdf(picked, {
                maxPages: limits.maxPages,
                isCurrent,
                onProgress: (done, total) =>
                    setFile({
                        status: 'reading',
                        name: picked.name,
                        done,
                        total,
                    }),
            });

            if (analysis !== null && isCurrent()) {
                setFile({
                    status: 'ready',
                    name: picked.name,
                    pages: analysis.pages,
                    coloredPages: analysis.coloredPages,
                });
            }
        } catch (error) {
            if (!isCurrent()) {
                return;
            }

            setFile({
                status: 'rejected',
                message:
                    error instanceof PdfTooLongError
                        ? `File ini ${formatNumber(error.pages)} halaman, sedangkan batasnya ${formatNumber(limits.maxPages)} halaman.`
                        : UNREADABLE_MESSAGE,
            });
        }
    }

    function choose(event: ChangeEvent<HTMLInputElement>) {
        const picked = event.target.files?.[0];

        event.target.value = '';

        if (picked) {
            void attach(picked);
        }
    }

    function remove() {
        latestPick.current++;
        onChange();
        setFile({ status: 'empty' });
    }

    function browse() {
        input.current?.click();
    }

    return {
        file,
        pages: file.status === 'ready' ? file.pages : null,
        coloredPages: file.status === 'ready' ? file.coloredPages : null,
        reading: file.status === 'reading',
        input,
        choose,
        remove,
        browse,
    };
}

export type PdfAttachmentState = ReturnType<typeof usePdfAttachment>;
