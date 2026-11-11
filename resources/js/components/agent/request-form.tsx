import { router } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';
import type { FormEvent, KeyboardEvent } from 'react';
import { useRef, useState } from 'react';
import { Fastener, paperShadow } from '@/components/directory/flyer-parts';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { parse } from '@/routes/agent';

const MAX_LENGTH = 500;

const examples = [
    'Print 20 halaman hitam putih A4, jilid spiral',
    'Print 5 halaman warna dan 30 halaman hitam putih, 2 rangkap',
    'Print 12 halaman warna ukuran A3',
];

const EMPTY_MESSAGE =
    'Tulis dulu kebutuhan cetakmu, misalnya jumlah halaman, hitam putih atau warna, dan jilidnya.';

/**
 * Form kebutuhan cetak. Tombolnya selalu bisa ditekan: kalau kotak masih kosong,
 * pengguna diberi tahu apa yang perlu ditulis, bukan dihadapkan pada tombol yang mati.
 */
export function RequestForm() {
    const [request, setRequest] = useState('');
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const field = useRef<HTMLTextAreaElement>(null);

    function change(value: string) {
        setRequest(value);
        setError(null);
    }

    function pick(example: string) {
        change(example);
        field.current?.focus();
    }

    function submit(event?: FormEvent) {
        event?.preventDefault();

        if (processing) {
            return;
        }

        if (request.trim() === '') {
            setError(EMPTY_MESSAGE);
            field.current?.focus();

            return;
        }

        setError(null);
        router.post(
            parse().url,
            { request },
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onError: (errors) => setError(errors.request ?? null),
            },
        );
    }

    function shortcut(event: KeyboardEvent) {
        if (event.key === 'Enter' && (event.metaKey || event.ctrlKey)) {
            submit();
        }
    }

    return (
        <form
            onSubmit={submit}
            noValidate
            className={`relative flex flex-col gap-4 rounded-sm bg-note-yellow p-5 sm:p-6 ${paperShadow}`}
        >
            <Fastener id={2} />

            <div className="flex flex-col gap-1.5">
                <label
                    htmlFor="kebutuhan-cetak"
                    className="font-display text-lg font-bold"
                >
                    Apa yang mau kamu cetak?
                </label>
                <textarea
                    id="kebutuhan-cetak"
                    ref={field}
                    value={request}
                    onChange={(event) => change(event.target.value)}
                    onKeyDown={shortcut}
                    maxLength={MAX_LENGTH}
                    rows={4}
                    placeholder="Misalnya: print 20 halaman hitam putih A4, jilid spiral"
                    aria-invalid={error !== null}
                    aria-describedby={error ? 'kebutuhan-error' : undefined}
                    className={`rounded-md border-2 bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring ${error ? 'border-destructive' : 'border-transparent'}`}
                />
                <div className="flex justify-between gap-3 text-xs text-foreground/70">
                    <span className="hidden sm:inline">
                        Tekan Ctrl + Enter untuk menghitung
                    </span>
                    <span className="ml-auto tabular-nums">
                        {request.length} / {MAX_LENGTH}
                    </span>
                </div>
            </div>

            <div className="flex flex-col gap-2">
                <p className="text-sm font-medium">
                    Atau coba salah satu contoh ini:
                </p>
                <ul className="flex flex-wrap gap-2">
                    {examples.map((example) => (
                        <li key={example}>
                            <button
                                type="button"
                                onClick={() => pick(example)}
                                className="inline-flex items-center gap-1.5 rounded-full border-2 border-dashed border-foreground/30 bg-white/60 px-3 py-1.5 text-left text-sm font-medium hover:border-solid hover:border-foreground hover:bg-white focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                <Sparkles
                                    className="size-3.5 shrink-0 text-primary"
                                    aria-hidden
                                />
                                {example}
                            </button>
                        </li>
                    ))}
                </ul>
            </div>

            {error && (
                <p
                    id="kebutuhan-error"
                    role="alert"
                    className="rounded-md bg-card px-3 py-2 text-sm font-medium text-destructive"
                >
                    {error}
                </p>
            )}

            <Button
                type="submit"
                size="lg"
                aria-disabled={processing}
                className="h-12 w-full gap-2 rounded-full border-2 border-foreground px-7 text-base font-bold shadow-[4px_4px_0_var(--foreground)] transition-[transform,box-shadow] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0_var(--foreground)] disabled:opacity-90 sm:ml-auto sm:w-auto"
                disabled={processing}
            >
                {processing ? (
                    <>
                        <Spinner />
                        Menghitung estimasi
                    </>
                ) : (
                    <>
                        Hitung estimasi
                        <ArrowRight aria-hidden />
                    </>
                )}
            </Button>
        </form>
    );
}
