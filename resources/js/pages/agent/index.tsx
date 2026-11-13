import { Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { EstimateList } from '@/components/agent/estimate-list';
import type { Estimate } from '@/components/agent/estimate-list';
import { PdfAttachment } from '@/components/agent/pdf-attachment';
import { SpecSummary } from '@/components/agent/spec-summary';
import type { PrintSpec } from '@/components/agent/spec-summary';
import { Fastener, paperShadow } from '@/components/directory/flyer-parts';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { usePdfAttachment } from '@/hooks/use-pdf-attachment';
import type { FileLimits } from '@/hooks/use-pdf-attachment';
import { parse } from '@/routes/agent';

const examples = [
    'Print 20 halaman hitam putih A4, jilid spiral',
    'Print 5 halaman warna dan 30 halaman hitam putih, 2 rangkap',
    'Print 12 halaman warna ukuran A3',
];

type Props = {
    fileLimits: FileLimits;
    spec?: PrintSpec;
    estimates?: Estimate[];
};

export default function AgentIndex({ fileLimits, spec, estimates }: Props) {
    const [request, setRequest] = useState('');
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const pdf = usePdfAttachment(fileLimits, () => setError(null));

    function submit(event: FormEvent) {
        event.preventDefault();
        setError(null);
        router.post(
            parse().url,
            { request, total_pages: pdf.pages },
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onError: (errors) =>
                    setError(errors.request ?? errors.total_pages ?? null),
            },
        );
    }

    return (
        <>
            <Head title="Asisten cetak" />
            <main className="mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-8 sm:py-12">
                <header className="flex flex-col gap-3">
                    <h1 className="font-display text-3xl leading-tight font-extrabold tracking-tight sm:text-5xl">
                        Tulis kebutuhan cetakmu
                    </h1>
                    <p className="max-w-xl text-muted-foreground">
                        Asisten membaca permintaanmu dan menyusun
                        spesifikasinya. Biayanya dihitung dari daftar harga tiap
                        tempat, bukan ditebak.
                    </p>
                </header>

                <form
                    onSubmit={submit}
                    className={`relative flex flex-col gap-3 rounded-sm bg-note-yellow p-5 sm:p-6 ${paperShadow}`}
                >
                    <Fastener id={2} />
                    <textarea
                        value={request}
                        onChange={(event) => setRequest(event.target.value)}
                        maxLength={500}
                        rows={3}
                        placeholder="Misalnya: print 20 halaman hitam putih A4, jilid spiral"
                        className="rounded-md border bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        aria-label="Kebutuhan cetak"
                        aria-invalid={error !== null}
                    />
                    <PdfAttachment pdf={pdf} />
                    <div className="flex flex-wrap gap-2">
                        {examples.map((example) => (
                            <button
                                key={example}
                                type="button"
                                onClick={() => setRequest(example)}
                                className="rounded-md bg-white/70 px-2.5 py-1 text-left text-sm hover:bg-white"
                            >
                                {example}
                            </button>
                        ))}
                    </div>
                    {error && (
                        <p role="alert" className="text-sm text-destructive">
                            {error}
                        </p>
                    )}
                    <Button
                        type="submit"
                        size="lg"
                        disabled={
                            request.trim() === '' || processing || pdf.reading
                        }
                    >
                        {processing && <Spinner />}
                        {processing ? 'Menghitung estimasi' : 'Cari estimasi'}
                    </Button>
                </form>

                {spec && (
                    <section className="flex flex-col gap-3">
                        <h2 className="font-display text-xl font-bold">
                            Yang kami pahami
                        </h2>
                        <SpecSummary spec={spec} />
                    </section>
                )}

                {estimates && (
                    <section className="flex flex-col gap-3">
                        <h2 className="font-display text-xl font-bold">
                            Estimasi per tempat
                        </h2>
                        <EstimateList estimates={estimates} />
                        <p className="text-sm text-muted-foreground">
                            Estimasi dari daftar harga yang tersimpan. Harga
                            bisa berbeda saat dicek langsung di tempat.
                        </p>
                    </section>
                )}
            </main>
        </>
    );
}
