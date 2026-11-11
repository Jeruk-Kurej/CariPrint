import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { OpeningHoursList } from '@/components/directory/opening-hours-list';
import { PriceTable } from '@/components/directory/price-table';
import { ShopMap } from '@/components/directory/shop-map';
import { WhatsAppButton } from '@/components/directory/whatsapp-button';
import { serviceLabel } from '@/lib/labels';
import { home } from '@/routes';
import type { OpenState } from '@/types';

type Shop = {
    id: number;
    slug: string;
    name: string;
    address: string;
    latitude: number;
    longitude: number;
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
const keepSelection = () => {};

function SectionTitle({ children }: { children: string }) {
    return <h2 className="font-display text-xl font-bold">{children}</h2>;
}

export default function ShopShow({ shop }: { shop: Shop }) {
    const today = dayKeys[new Date().getDay()];
    const marker = { ...shop, distanceKm: null, startingPrice: null };

    return (
        <>
            <Head title={shop.name} />
            <main className="flex flex-col gap-8 px-4 py-6 sm:py-10">
                <Link
                    href={home()}
                    className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft className="size-4" aria-hidden /> Semua tempat
                </Link>

                <header className="flex flex-col gap-3 border-b pb-6">
                    <OpenStatusBadgeRow shop={shop} />
                    <h1 className="font-display text-4xl leading-[1.05] font-extrabold tracking-tight sm:text-5xl">
                        {shop.name}
                    </h1>
                    <p className="text-muted-foreground">{shop.address}</p>
                    {shop.whatsapp && (
                        <div>
                            <WhatsAppButton
                                phone={shop.whatsapp}
                                message={`Halo ${shop.name}, saya mau tanya harga printing.`}
                            />
                        </div>
                    )}
                </header>

                <div className="grid items-start gap-8 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
                    <section className="flex flex-col gap-3">
                        <SectionTitle>Daftar harga</SectionTitle>
                        <PriceTable prices={shop.prices} />
                        <p className="text-sm text-muted-foreground">
                            <span className="font-medium text-foreground">
                                Terverifikasi
                            </span>{' '}
                            berarti dari survei tim CariPrint.{' '}
                            <span className="font-medium text-foreground">
                                Perkiraan
                            </span>{' '}
                            berarti belum disurvei dan bisa berbeda di lapangan.
                            {shop.updatedAt &&
                                ` Data terakhir diperbarui ${shop.updatedAt}.`}
                        </p>
                    </section>

                    <aside className="flex flex-col gap-8">
                        <section className="flex flex-col gap-3">
                            <SectionTitle>Jam buka</SectionTitle>
                            <OpeningHoursList
                                hours={shop.openingHours}
                                today={today}
                            />
                        </section>

                        <section className="flex flex-col gap-3">
                            <SectionTitle>Layanan</SectionTitle>
                            <ul className="flex flex-wrap gap-2">
                                {shop.services.map((service) => (
                                    <li
                                        key={service}
                                        className="rounded-md bg-secondary px-2.5 py-1 text-sm font-medium"
                                    >
                                        {serviceLabel(service)}
                                    </li>
                                ))}
                            </ul>
                        </section>

                        <section className="flex flex-col gap-3">
                            <SectionTitle>Lokasi</SectionTitle>
                            <ShopMap
                                shops={[marker]}
                                selectedId={shop.id}
                                onSelect={keepSelection}
                                heightClass="h-64"
                            />
                        </section>
                    </aside>
                </div>
            </main>
        </>
    );
}

/** Status buka dan kapan berubah, ditaruh paling atas karena itu yang dicari lebih dulu. */
function OpenStatusBadgeRow({ shop }: { shop: Shop }) {
    return (
        <div className="flex flex-wrap items-center gap-2 text-sm">
            <OpenStatusBadge state={shop.openState} />
            {shop.openState === 'closed' && shop.opensAt && (
                <span>Buka lagi {shop.opensAt}</span>
            )}
            {shop.openState !== 'closed' && shop.closesAt && (
                <span>Tutup pukul {shop.closesAt}</span>
            )}
        </div>
    );
}
