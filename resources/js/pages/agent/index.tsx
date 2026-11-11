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
