import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { DirectionsButton } from '@/components/directory/directions-button';
import { Fastener, paperFor } from '@/components/directory/flyer-parts';
import { OpenStatusLine } from '@/components/directory/open-status-line';
import { OpeningHoursList } from '@/components/directory/opening-hours-list';
import { PriceTable } from '@/components/directory/price-table';
import { ShopMap } from '@/components/directory/shop-map';
import { WhatsAppButton } from '@/components/directory/whatsapp-button';
import { PageTitle, SectionHeading } from '@/components/page-header';
import { serviceLabel } from '@/lib/labels';
import { todayKey } from '@/lib/weekday';
import { home } from '@/routes';
import type { ShopDetail } from '@/types';

export default function ShopShow({ shop }: { shop: ShopDetail }) {
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

                <header
                    className={`relative mt-2 flex flex-col gap-3 rounded-sm p-6 paper-shadow sm:p-8 ${paperFor(shop.id)}`}
                >
                    <Fastener id={shop.id} />
                    <OpenStatusLine shop={shop} />
                    <PageTitle>{shop.name}</PageTitle>
                    <p className="text-muted-foreground">{shop.address}</p>
                    <div className="flex flex-wrap gap-2">
                        <DirectionsButton
                            latitude={shop.latitude}
                            longitude={shop.longitude}
                        />
                        {shop.whatsapp && (
                            <WhatsAppButton
                                phone={shop.whatsapp}
                                message={`Halo ${shop.name}, saya mau tanya harga printing.`}
                            />
                        )}
                    </div>
                </header>

                <div className="grid items-start gap-8 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
                    <section className="flex flex-col gap-3">
                        <SectionHeading>Daftar harga</SectionHeading>
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
                            <SectionHeading>Jam buka</SectionHeading>
                            <OpeningHoursList
                                hours={shop.openingHours}
                                today={todayKey()}
                            />
                        </section>

                        <section className="flex flex-col gap-3">
                            <SectionHeading>Layanan</SectionHeading>
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
                    </aside>
                </div>

                <section className="flex flex-col gap-3">
                    <SectionHeading>Lokasi</SectionHeading>
                    <div className="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] lg:gap-8">
                        <ShopMap
                            shops={[marker]}
                            selectedId={shop.id}
                            heightClass="h-64 lg:h-52"
                        />
                        <div className="dog-ear relative flex flex-col items-start gap-3 rounded-sm bg-note-sky p-4 paper-shadow lg:justify-between lg:p-6">
                            <p className="font-medium lg:text-lg">
                                {shop.address}
                            </p>
                            <DirectionsButton
                                latitude={shop.latitude}
                                longitude={shop.longitude}
                                variant="outline"
                            />
                        </div>
                    </div>
                </section>
            </main>
        </>
    );
}
