import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { OpeningHoursList } from '@/components/directory/opening-hours-list';
import { PriceTable } from '@/components/directory/price-table';
import { WhatsAppButton } from '@/components/directory/whatsapp-button';
import { home } from '@/routes';
import type { OpenState } from '@/types';

type Shop = {
    name: string;
    address: string;
    whatsapp: string | null;
    openState: OpenState;
    closesAt: string | null;
    opensAt: string | null;
    openingHours: Record<string, { open: string; close: string }[]>;
    services: string[];
    prices: {
        service: string;
        paperSize: string | null;
        colorMode: string | null;
        price: number;
        unit: string;
        source: 'verified' | 'estimated';
    }[];
    updatedAt: string | null;
};

const dayKeys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

export default function ShopShow({ shop }: { shop: Shop }) {
    const today = dayKeys[new Date().getDay()];

    return (
        <>
            <Head title={shop.name} />
            <main className="mx-auto flex w-full max-w-3xl flex-col gap-5 p-4">
                <Link
                    href={home()}
                    className="inline-flex items-center gap-1 text-sm text-muted-foreground"
                >
                    <ArrowLeft className="size-4" /> Semua tempat
                </Link>

                <header className="flex flex-col gap-2">
                    <div className="flex items-start justify-between gap-3">
                        <h1 className="font-display text-3xl leading-tight font-bold tracking-tight">
                            {shop.name}
                        </h1>
                        <OpenStatusBadge state={shop.openState} />
                    </div>
                    <p className="text-sm text-muted-foreground">
                        {shop.address}
                    </p>
                    {shop.openState === 'closed' && shop.opensAt && (
                        <p className="text-sm">Buka lagi: {shop.opensAt}</p>
                    )}
                    {shop.openState !== 'closed' && shop.closesAt && (
                        <p className="text-sm">Tutup pukul {shop.closesAt}</p>
                    )}
                </header>

                {shop.whatsapp && (
                    <WhatsAppButton
                        phone={shop.whatsapp}
                        message={`Halo ${shop.name}, saya mau tanya harga printing.`}
                    />
                )}

                <section className="flex flex-col gap-2">
                    <h2 className="font-display text-lg font-semibold">
                        Daftar harga
                    </h2>
                    <p className="text-xs text-muted-foreground">
                        <span className="font-medium text-foreground">
                            Terverifikasi
                        </span>{' '}
                        = dari survei tim CariPrint.{' '}
                        <span className="font-medium text-foreground">
                            Perkiraan
                        </span>{' '}
                        = belum disurvei, bisa berbeda di lapangan.
                    </p>
                    <PriceTable prices={shop.prices} />
                    {shop.updatedAt && (
                        <p className="text-xs text-muted-foreground">
                            Data terakhir diperbarui: {shop.updatedAt}
                        </p>
                    )}
                </section>

                <section className="flex flex-col gap-2">
                    <h2 className="font-display text-lg font-semibold">
                        Layanan
                    </h2>
                    <p className="text-sm">{shop.services.join(' · ')}</p>
                </section>

                <section className="flex flex-col gap-2">
                    <h2 className="font-display text-lg font-semibold">
                        Jam buka
                    </h2>
                    <OpeningHoursList hours={shop.openingHours} today={today} />
                </section>
            </main>
        </>
    );
}
