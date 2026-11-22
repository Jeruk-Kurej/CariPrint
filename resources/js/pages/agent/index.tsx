import { Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useRef, useState } from 'react';
import { ExamplePrompts } from '@/components/agent/example-prompts';
import { EstimateList } from '@/components/agent/estimate-list';
import type { Estimate } from '@/components/agent/estimate-list';
import { PdfAttachment } from '@/components/agent/pdf-attachment';
import { QuotaNote } from '@/components/agent/quota-note';
import type { AiQuotaStatus } from '@/components/agent/quota-note';
import { SpecSummary } from '@/components/agent/spec-summary';
import type { PrintSpec } from '@/components/agent/spec-summary';
import { Fastener } from '@/components/directory/flyer-parts';
import { PageTitle } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useGeolocation } from '@/hooks/use-geolocation';
import { usePdfAttachment } from '@/hooks/use-pdf-attachment';
import type { FileLimits } from '@/hooks/use-pdf-attachment';
import { parse } from '@/routes/agent';

type Props = {
    fileLimits: FileLimits;
    quota: AiQuotaStatus;
    spec?: PrintSpec;
    estimates?: Estimate[];
    shopsWithoutPrices?: number;
};

export default function AgentIndex({
    fileLimits,
    quota,
    spec,
    estimates,
    shopsWithoutPrices = 0,
}: Props) {
    const [request, setRequest] = useState('');
    const textarea = useRef<HTMLTextAreaElement>(null);
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const pdf = usePdfAttachment(fileLimits, () => setError(null));
    const location = useGeolocation();

    function fillRequest(text: string) {
        setRequest(text);
        setError(null);
        textarea.current?.focus();
    }

    function submit(event: FormEvent) {
        event.preventDefault();
        setError(null);
        router.post(
            parse().url,
            {
                request,
                total_pages: pdf.pages,
                colored_pages: pdf.coloredPages,
                latitude:
                    location.status === 'granted' ? location.latitude : null,
                longitude:
                    location.status === 'granted' ? location.longitude : null,
            },
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onError: (errors) =>
                    setError(
                        errors.request ??
                            errors.total_pages ??
                            errors.colored_pages ??
                            null,
                    ),
            },
        );
    }

    return (
        <>
            <Head title="Asisten cetak" />
            <main className="flex w-full flex-col gap-8 px-4 py-8 sm:py-12">
                <header className="flex flex-col gap-3">
                    <PageTitle>Mau print apa hari ini?</PageTitle>
                    <p className="max-w-xl text-muted-foreground">
                        Ceritakan dengan kalimat biasa: ukuran kertas (A4, F4,
                        A3), hitam-putih atau warna, jumlah rangkap, jilid, atau
                        halaman tertentu. Asisten menyusun spesifikasinya, lalu
                        biayanya dihitung dari daftar harga tiap tempat, bukan
                        ditebak.
                    </p>
                </header>

                <form
                    onSubmit={submit}
                    className="relative flex flex-col gap-3 rounded-sm bg-note-yellow p-5 paper-shadow sm:p-6"
                >
                    <Fastener id={2} />
                    <ExamplePrompts
                        hasPdf={pdf.pages !== null}
                        onPick={fillRequest}
                    />
                    <textarea
                        ref={textarea}
                        value={request}
                        onChange={(event) => setRequest(event.target.value)}
                        maxLength={500}
                        rows={3}
                        placeholder={
                            pdf.pages !== null
                                ? 'Misalnya: print semua hitam putih, warna hanya yang perlu, jilid spiral'
                                : 'Misalnya: print 20 halaman hitam putih A4, 2 rangkap, jilid spiral'
                        }
                        className="rounded-md border bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        aria-label="Kebutuhan cetak"
                        aria-invalid={error !== null}
                    />
                    <PdfAttachment pdf={pdf} />
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
                    <QuotaNote quota={quota} />
                    {location.status !== 'granted' && (
                        <p
                            aria-live="polite"
                            className="text-sm text-foreground/70"
                        >
                            {location.status === 'loading'
                                ? 'Mencari lokasimu. Jarak tampil kalau lokasi diizinkan.'
                                : 'Lokasi tidak aktif, jadi jarak tidak ditampilkan.'}
                        </p>
                    )}
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
                            bisa berbeda saat dicek langsung di tempat.{' '}
                            {estimates.some((shop) => shop.distanceKm !== null)
                                ? 'Jarak dihitung garis lurus dari lokasimu, dan lokasimu tidak disimpan.'
                                : 'Izinkan lokasi di browser lalu cari lagi untuk melihat jarak tiap tempat.'}
                        </p>
                        {shopsWithoutPrices > 0 && (
                            <p className="text-sm text-muted-foreground">
                                {shopsWithoutPrices} tempat lain belum punya
                                daftar harga, jadi tidak ikut dihitung.
                            </p>
                        )}
                    </section>
                )}
            </main>
        </>
    );
}
