import { useHttp } from '@inertiajs/react';
import { Sparkles } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import type { DirectoryChoices } from '@/lib/directory-url';
import { aiSearch } from '@/routes/directory';

export type AiQuota = {
    limit: number | null;
    remaining: number | null;
    isGuest: boolean;
};

type Answer = {
    filters: DirectoryChoices | null;
    area: string;
    quota: AiQuota;
};

type Props = {
    /** Isi kotak pencarian, yang dibaca sebagai kalimat. */
    sentence: string;
    quota: AiQuota;
    hasDistance: boolean;
    onApply: (filters: DirectoryChoices) => void;
    onReset: () => void;
};

/** Tombol yang meminta AI mengubah kalimat jadi filter biasa, yang tetap bisa diubah. Lokasi tidak dikirim. */
export function AiSearch({
    sentence,
    quota,
    hasDistance,
    onApply,
    onReset,
}: Props) {
    const http = useHttp<{ sentence: string }, Answer>({ sentence: '' });
    const [answer, setAnswer] = useState<Answer | null>(null);
    const [failed, setFailed] = useState(false);
    const [remaining, setRemaining] = useState(quota.remaining);

    async function search() {
        setAnswer(null);
        setFailed(false);
        http.transform(() => ({ sentence: sentence.trim() }));

        try {
            const result = await http.post(aiSearch().url);
            setAnswer(result);
            setRemaining(result.quota.remaining);

            if (result.filters) {
                onApply(result.filters);
            }
        } catch {
            setFailed(true);
        }
    }

    const error =
        http.errors.sentence ??
        (failed
            ? 'Cari pakai AI gagal. Cek koneksimu, atau pakai filter biasa.'
            : null);

    return (
        <div className="flex flex-col gap-1.5">
            <div className="flex flex-wrap items-center gap-x-3 gap-y-1">
                <Button
                    type="button"
                    variant="outline"
                    onClick={search}
                    disabled={http.processing || sentence.trim() === ''}
                    className="min-h-11 rounded-full border-2 font-semibold"
                >
                    <Sparkles aria-hidden />
                    {http.processing ? 'Membaca...' : 'Cari pakai AI'}
                </Button>
                <p className="min-w-0 flex-1 text-xs text-muted-foreground">
                    Bisa tulis kalimat, misalnya “print F4 termurah di Ngagel”.
                    {remaining !== null && ` Sisa jatah AI: ${remaining}.`}
                </p>
            </div>
            <div aria-live="polite" className="text-xs">
                {error && <p className="text-destructive">{error}</p>}
                {answer && !answer.filters && (
                    <p>
                        Tidak ada filter yang bisa dibaca dari kalimat itu. Coba
                        sebut area, ukuran kertas, atau layanan.
                    </p>
                )}
                {answer?.filters && (
                    <p>
                        Filter sudah diisi, ubah kalau kurang pas.
                        {answer.area &&
                            ` Area dicari dari alamat yang berisi “${answer.area}”, jadi tempat di dekatnya dengan alamat lain tidak ikut.`}
                        {answer.filters.maxKm !== null &&
                            !hasDistance &&
                            ' Batas jarak belum dipakai karena lokasi mati.'}{' '}
                        <button
                            type="button"
                            onClick={() => {
                                setAnswer(null);
                                onReset();
                            }}
                            className="-my-3 inline-block py-3 font-semibold underline"
                        >
                            Hapus semua filter
                        </button>
                    </p>
                )}
            </div>
        </div>
    );
}
