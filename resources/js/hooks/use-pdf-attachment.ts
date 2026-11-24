import { useHttp } from '@inertiajs/react';
import type { ChangeEvent } from 'react';
import { useEffect, useRef, useState } from 'react';
import { formatNumber } from '@/lib/format';
import type {
    ColorNeed,
    ColorSavings,
    FileCheckResult,
} from '@/lib/file-check';
import { PdfTooLongError, analyzePdf } from '@/lib/pdf';
import { fileCheck } from '@/routes/agent';

export type FileLimits = {
    maxMegabytes: number;
    maxPages: number;
};

export type AttachedPdf =
    | { status: 'empty' }
    | { status: 'reading'; name: string; done: number; total: number | null }
    | {
          status: 'ready';
          name: string;
          pages: number;
          /** Penilaian server tiap halaman. Null selama cek pertama masih berjalan. */
          needs: ColorNeed[] | null;
          /** Halaman yang disarankan berwarna oleh server. */
          suggested: number[];
          /** Halaman yang dipilih pengguna untuk dicetak berwarna. Inilah yang dipakai untuk estimasi. */
          chosen: number[];
          savings: ColorSavings | null;
      }
    | { status: 'rejected'; message: string };

type Payload = { shares: number[]; chosen: number[] | null };

const UNREADABLE_MESSAGE =
    'File ini tidak bisa dibaca. Pastikan filenya PDF yang tidak rusak dan tidak dikunci kata sandi.';

const CHECK_FAILED_MESSAGE =
    'Cek warna file gagal. Coba lampirkan lagi sebentar lagi.';

/** Jeda sebelum hemat dihitung ulang, supaya beberapa ketukan berturut-turut cukup satu permintaan. */
const SAVINGS_DELAY_MS = 400;

function isPdf(file: File): boolean {
    return file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
}

/**
 * Lampiran PDF untuk asisten cetak. File dibaca di perangkat pengguna untuk menghitung halamannya dan
 * mengukur warna tiap halaman, dan tidak pernah dikirim ke server. Yang dikirim cuma angka: berapa persen
 * tiap halaman berwarna. Server menilai halaman mana yang perlu warna dan berapa hematnya, lalu pengguna
 * yang menentukan pilihan akhirnya.
 * onChange dipanggil setiap kali pengguna memilih atau menghapus file.
 */
export function usePdfAttachment(limits: FileLimits, onChange: () => void) {
    const [file, setFile] = useState<AttachedPdf>({ status: 'empty' });
    const shares = useRef<number[]>([]);
    const input = useRef<HTMLInputElement>(null);
    const latestPick = useRef(0);
    const latestCheck = useRef(0);
    const savingsTimer = useRef<ReturnType<typeof setTimeout>>(undefined);
    const http = useHttp<Payload, FileCheckResult>({
        shares: [],
        chosen: null,
    });
    const { transform, post } = http;

    useEffect(() => () => clearTimeout(savingsTimer.current), []);

    async function check(chosen: number[] | null): Promise<FileCheckResult> {
        transform(() => ({ shares: shares.current, chosen }));

        return post(fileCheck().url);
    }

    async function attach(picked: File) {
        const pick = ++latestPick.current;
        const isCurrent = () => pick === latestPick.current;

        clearTimeout(savingsTimer.current);
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

            if (analysis === null || !isCurrent()) {
                return;
            }

            shares.current = analysis.shares;
            setFile({
                status: 'ready',
                name: picked.name,
                pages: analysis.pages,
                needs: null,
                suggested: [],
                chosen: [],
                savings: null,
            });

            let result: FileCheckResult;

            try {
                result = await check(null);
            } catch {
                if (isCurrent()) {
                    setFile({
                        status: 'rejected',
                        message: CHECK_FAILED_MESSAGE,
                    });
                }

                return;
            }

            if (isCurrent()) {
                setFile({
                    status: 'ready',
                    name: picked.name,
                    pages: analysis.pages,
                    needs: result.needs,
                    suggested: result.suggested,
                    chosen: result.suggested,
                    savings: result.savings,
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

    /** Mengganti pilihan halaman berwarna, lalu menghitung ulang hematnya sesudah jeda singkat. */
    function setChosen(pages: number[]) {
        setFile((current) =>
            current.status === 'ready' && current.needs !== null
                ? { ...current, chosen: pages }
                : current,
        );

        const fileNumber = latestPick.current;
        const requestNumber = ++latestCheck.current;

        clearTimeout(savingsTimer.current);
        savingsTimer.current = setTimeout(() => {
            check(pages)
                .then((result) => {
                    if (
                        fileNumber === latestPick.current &&
                        requestNumber === latestCheck.current
                    ) {
                        setFile((current) =>
                            current.status === 'ready'
                                ? { ...current, savings: result.savings }
                                : current,
                        );
                    }
                })
                .catch(() => undefined);
        }, SAVINGS_DELAY_MS);
    }

    function toggle(page: number) {
        if (file.status !== 'ready') {
            return;
        }

        setChosen(
            file.chosen.includes(page)
                ? file.chosen.filter((item) => item !== page)
                : [...file.chosen, page].sort((a, b) => a - b),
        );
    }

    function selectFile(event: ChangeEvent<HTMLInputElement>) {
        const picked = event.target.files?.[0];

        event.target.value = '';

        if (picked) {
            void attach(picked);
        }
    }

    function remove() {
        latestPick.current++;
        clearTimeout(savingsTimer.current);
        onChange();
        setFile({ status: 'empty' });
    }

    function browse() {
        input.current?.click();
    }

    const ready = file.status === 'ready' ? file : null;

    return {
        file,
        pages: ready?.pages ?? null,
        /** Halaman yang dipilih untuk dicetak berwarna, atau null kalau belum ada file yang siap. */
        coloredPages:
            ready !== null && ready.needs !== null ? ready.chosen : null,
        reading:
            file.status === 'reading' ||
            (ready !== null && ready.needs === null),
        input,
        selectFile,
        remove,
        browse,
        toggle,
        setChosen,
    };
}

export type PdfAttachmentState = ReturnType<typeof usePdfAttachment>;
