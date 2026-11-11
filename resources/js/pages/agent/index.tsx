import { Head } from '@inertiajs/react';
import { EstimateList } from '@/components/agent/estimate-list';
import type { Estimate } from '@/components/agent/estimate-list';
import { RequestForm } from '@/components/agent/request-form';
import { PageHeader, SectionHeading } from '@/components/page-header';
import { SpecSummary } from '@/components/agent/spec-summary';
import type { PrintSpec } from '@/components/agent/spec-summary';

type Props = {
    spec?: PrintSpec;
    estimates?: Estimate[];
};

export default function AgentIndex({ spec, estimates }: Props) {
    return (
        <>
            <Head title="Asisten cetak" />
            <main className="mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-8 sm:py-12">
                <PageHeader
                    title="Tulis kebutuhan cetakmu"
                    intro="Asisten membaca permintaanmu dan menyusun spesifikasinya. Biayanya dihitung dari daftar harga tiap tempat, bukan ditebak."
                />

                <RequestForm />

                {spec && (
                    <section className="flex flex-col gap-3">
                        <SectionHeading>Yang kami pahami</SectionHeading>
                        <SpecSummary spec={spec} />
                    </section>
                )}

                {estimates && (
                    <section className="flex flex-col gap-3">
                        <SectionHeading>Estimasi per tempat</SectionHeading>
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
import { Head, router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';
import { EstimateList } from '@/components/agent/estimate-list';
import { Button } from '@/components/ui/button';
import { parse } from '@/routes/agent';

type Spec = {
    paperSize: string;
    bwPages: number;
    colorPages: number;
    copies: number;
    binding: string | null;
};

type Estimate = {
    name: string;
    slug: string;
    total: number | null;
    missing: string[];
};

export default function AgentIndex({
    spec,
    estimates,
}: {
    spec?: Spec;
    estimates?: Estimate[];
}) {
    const [request, setRequest] = useState('');

    function submit(event: FormEvent) {
        event.preventDefault();
        router.post(parse().url, { request }, { preserveScroll: true });
    }

    return (
        <>
            <Head title="Asisten cetak" />
            <main className="mx-auto flex w-full max-w-3xl flex-col gap-5 p-4">
                <h1 className="font-display text-3xl leading-tight font-bold tracking-tight">
                    Tulis kebutuhan cetakmu
                </h1>
                <p className="text-sm text-muted-foreground">
                    Contoh: "Print halaman 1–10, hitam putih, A4, jilid spiral.
                    Cari yang paling murah."
                </p>

                <form onSubmit={submit} className="flex flex-col gap-3">
                    <textarea
                        value={request}
                        onChange={(e) => setRequest(e.target.value)}
                        maxLength={500}
                        rows={3}
                        className="rounded-lg border bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        aria-label="Kebutuhan cetak"
                    />
                    <Button type="submit" disabled={request.trim() === ''}>
                        Cari estimasi
                    </Button>
                </form>

                {spec && (
                    <section className="flex flex-col gap-2 text-sm">
                        <h2 className="font-display text-lg font-semibold">
                            Yang kami pahami
                        </h2>
                        <p>
                            Kertas {spec.paperSize.toUpperCase()} ·{' '}
                            {spec.bwPages} hal. hitam putih · {spec.colorPages}{' '}
                            hal. warna · {spec.copies} rangkap
                            {spec.binding ? ` · ${spec.binding}` : ''}
                        </p>
                    </section>
                )}

                {estimates && (
                    <section className="flex flex-col gap-2">
                        <h2 className="font-display text-lg font-semibold">
                            Estimasi per tempat
                        </h2>
                        <EstimateList estimates={estimates} />
                        <p className="text-xs text-muted-foreground">
                            Estimasi dari daftar harga yang tersimpan. Harga
                            bisa berbeda saat dicek langsung di tempat.
                        </p>
                    </section>
                )}
            </main>
        </>
    );
}
