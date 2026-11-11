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
                <h1 className="text-xl font-semibold">
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
                        className="rounded-xl border bg-background p-3 text-sm"
                        aria-label="Kebutuhan cetak"
                    />
                    <Button type="submit" disabled={request.trim() === ''}>
                        Cari estimasi
                    </Button>
                </form>

                {spec && (
                    <section className="flex flex-col gap-2 text-sm">
                        <h2 className="font-semibold">Yang kami pahami</h2>
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
                        <h2 className="font-semibold">Estimasi per tempat</h2>
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
