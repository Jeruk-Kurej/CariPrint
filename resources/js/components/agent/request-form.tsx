import { ArrowRight, Sparkles } from 'lucide-react';
import { Fastener } from '@/components/directory/flyer-parts';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { MAX_REQUEST_LENGTH, usePrintRequest } from '@/hooks/use-print-request';

const examples = [
    'Print 20 halaman hitam putih A4, jilid spiral',
    'Print 5 halaman warna dan 30 halaman hitam putih, 2 rangkap',
    'Print 12 halaman warna ukuran A3',
];

/** Form kebutuhan cetak. Seluruh logikanya ada di usePrintRequest, di sini hanya tampilan. */
export function RequestForm() {
    const form = usePrintRequest();

    return (
        <form
            onSubmit={form.submit}
            noValidate
            className="relative flex flex-col gap-4 rounded-sm bg-note-yellow p-5 paper-shadow sm:p-6"
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
                    ref={form.field}
                    value={form.request}
                    onChange={(event) => form.change(event.target.value)}
                    onKeyDown={form.submitOnShortcut}
                    maxLength={MAX_REQUEST_LENGTH}
                    rows={4}
                    placeholder="Misalnya: print 20 halaman hitam putih A4, jilid spiral"
                    aria-invalid={form.error !== null}
                    aria-describedby={
                        form.error ? 'kebutuhan-error' : undefined
                    }
                    className={`rounded-md border-2 bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring ${form.error ? 'border-destructive' : 'border-transparent'}`}
                />
                <div className="flex justify-between gap-3 text-xs text-foreground/70">
                    <span className="hidden sm:inline">
                        Tekan Ctrl + Enter untuk menghitung
                    </span>
                    <span className="ml-auto tabular-nums">
                        {form.request.length} / {MAX_REQUEST_LENGTH}
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
                                onClick={() => form.pick(example)}
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

            {form.error && (
                <p
                    id="kebutuhan-error"
                    role="alert"
                    className="rounded-md bg-card px-3 py-2 text-sm font-medium text-destructive"
                >
                    {form.error}
                </p>
            )}

            <Button
                type="submit"
                size="lg"
                disabled={form.processing}
                className="h-12 w-full sticker-press gap-2 rounded-full sticker px-7 text-base font-bold disabled:opacity-90 sm:ml-auto sm:w-auto"
            >
                {form.processing ? (
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
