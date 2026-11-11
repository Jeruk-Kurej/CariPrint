import { Head } from '@inertiajs/react';
import { EstimateList } from '@/components/agent/estimate-list';
import type { Estimate } from '@/components/agent/estimate-list';
import { RequestForm } from '@/components/agent/request-form';
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

                <RequestForm />

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
