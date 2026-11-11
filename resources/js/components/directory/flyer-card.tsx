import { Link } from '@inertiajs/react';
import { MapPin, MessageCircle } from 'lucide-react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { formatDistance, formatRupiah } from '@/lib/format';
import { serviceLabel } from '@/lib/labels';
import { show } from '@/routes/directory';
import type { ShopSummary } from '@/types';

const papers = [
    'bg-note-yellow',
    'bg-note-pink',
    'bg-note-mint',
    'bg-note-sky',
];
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
 * disobek-sobek: harga, WhatsApp, dan penanda peta.
 */
export function FlyerCard({
    shop,
    selected = false,
    onSelect,
    tilted = true,
}: Props) {
    const paper = papers[shop.id % papers.length];
    const tilt = tilted ? tilts[(shop.id * 3) % tilts.length] : '';

    return (
        <article
            className={`group relative flex flex-col rounded-sm text-foreground shadow-[0_1px_0_rgba(29,33,71,0.15),0_10px_20px_-10px_rgba(29,33,71,0.35)] hover:z-10 hover:-translate-y-1 hover:rotate-0 motion-safe:transition-transform motion-safe:duration-200 ${paper} ${tilt} ${selected ? 'ring-2 ring-primary' : ''}`}
        >
            <span
                aria-hidden
                className="absolute -top-2 left-1/2 size-4 -translate-x-1/2 rounded-full bg-primary shadow-[0_2px_0_rgba(0,0,0,0.25)] ring-2 ring-white"
            />
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
            <div className="relative z-10 flex divide-x-2 divide-dashed divide-foreground/25 border-t-2 border-dashed border-foreground/25">
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
