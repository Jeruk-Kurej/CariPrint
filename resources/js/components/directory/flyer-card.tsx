import { Link } from '@inertiajs/react';
import { MapPin, MessageCircle, Navigation, Scissors } from 'lucide-react';
import { Fastener, paperFor } from '@/components/directory/flyer-parts';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { formatDistance, formatRupiah } from '@/lib/format';
import { serviceLabel } from '@/lib/labels';
import { directionsUrl } from '@/lib/maps';
import { show } from '@/routes/directory';
import type { ShopSummary } from '@/types';

const tilts = [
    '-rotate-[1.2deg]',
    'rotate-[0.9deg]',
    '-rotate-[0.5deg]',
    'rotate-[1.4deg]',
];

type Props = {
    shop: ShopSummary;
    selected?: boolean;
    /** Kalau diisi, pamflet punya sobekan "Di peta" untuk menandai tempat ini di peta. */
    onSelect?: (id: number) => void;
    tilted?: boolean;
};

const tab =
    'flex flex-col justify-center gap-0.5 px-3 py-2.5 text-left text-sm font-semibold';

/**
 * Satu tempat print sebagai pamflet di papan pengumuman kampus. Bagian bawahnya
 * disobek-sobek: harga, WhatsApp, rute, dan penanda peta.
 */
export function FlyerCard({
    shop,
    selected = false,
    onSelect,
    tilted = true,
}: Props) {
    const paper = paperFor(shop.id);
    const tilt = tilted ? tilts[(shop.id * 3) % tilts.length] : '';

    return (
        <article
            className={`group relative flex flex-col rounded-sm text-foreground paper-shadow hover:z-10 hover:-translate-y-1 hover:rotate-0 motion-safe:transition-transform motion-safe:duration-200 ${paper} ${tilt} ${selected ? 'ring-2 ring-primary' : ''}`}
        >
            <Fastener id={shop.id} />
            <div className="flex flex-1 flex-col gap-2 p-4 pt-5">
                <h3 className="font-display text-2xl leading-tight font-extrabold">
                    <Link
                        href={show({ shop: shop.slug }).url}
                        className="after:absolute after:inset-0"
                    >
                        {shop.name}
                    </Link>
                </h3>
                <p className="text-sm text-foreground/70">{shop.address}</p>
                <div className="flex flex-wrap items-center gap-2">
                    <OpenStatusBadge state={shop.openState} />
                    {shop.distanceKm !== null && (
                        <span className="inline-flex items-center gap-1 text-sm font-semibold tabular-nums">
                            <MapPin className="size-3.5" aria-hidden />
                            {formatDistance(shop.distanceKm)}
                        </span>
                    )}
                </div>
                <ul className="flex flex-wrap gap-1.5">
                    {shop.services.slice(0, 3).map((service) => (
                        <li
                            key={service}
                            className="rounded-full bg-white/70 px-2 py-0.5 text-xs font-medium"
                        >
                            {serviceLabel(service)}
                        </li>
                    ))}
                </ul>
            </div>
            <div className="relative z-10 flex divide-x-2 divide-dashed divide-foreground/25 border-t-2 border-dashed border-foreground/25 bg-inherit">
                <Scissors
                    aria-hidden
                    className="absolute -top-[11px] left-3 size-5 rotate-0 bg-inherit px-0.5 text-foreground/55"
                />
                <div className={`${tab} flex-1`}>
                    <span className="text-xs font-medium text-foreground/70">
                        Mulai dari
                    </span>
                    <span className="w-fit rounded-sm bg-highlight px-1.5 font-display text-xl font-extrabold tabular-nums">
                        {shop.startingPrice === null
                            ? '–'
                            : formatRupiah(shop.startingPrice)}
                    </span>
                </div>
                {shop.whatsapp && (
                    <a
                        href={`https://wa.me/${shop.whatsapp}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={`${tab} items-center hover:bg-white/50`}
                    >
                        <MessageCircle className="size-5" aria-hidden />
                        WhatsApp
                    </a>
                )}
                <a
                    href={directionsUrl(shop.latitude, shop.longitude)}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={`${tab} items-center hover:bg-white/50`}
                >
                    <Navigation className="size-5" aria-hidden />
                    Rute
                    <span className="sr-only"> di Google Maps (tab baru)</span>
                </a>
                {onSelect && (
                    <button
                        type="button"
                        onClick={() => onSelect(shop.id)}
                        aria-pressed={selected}
                        className={`${tab} items-center hover:bg-white/50`}
                    >
                        <MapPin className="size-5" aria-hidden />
                        Di peta
                    </button>
                )}
            </div>
        </article>
    );
}
