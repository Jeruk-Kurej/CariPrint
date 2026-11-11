import { Link } from '@inertiajs/react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { Button } from '@/components/ui/button';
import { formatRupiah } from '@/lib/format';
import { index as agentIndex } from '@/routes/agent';
import type { ShopSummary } from '@/types';

type Props = {
    shops: ShopSummary[];
    openCount: number;
};

/** Tanda potong cetak di latar hero, penanda dunia percetakan. */
function CropMarks() {
    return (
        <svg
            aria-hidden
            className="pointer-events-none absolute inset-0 size-full text-white/10"
            fill="none"
            stroke="currentColor"
            strokeWidth="1"
        >
            <path d="M32 0v48M0 32h48" />
        </svg>
    );
}

function Stat({ value, label }: { value: string; label: string }) {
    return (
        <div>
            <dt className="text-sm text-primary-foreground/70">{label}</dt>
            <dd className="font-display text-3xl font-bold tabular-nums">
                {value}
            </dd>
        </div>
    );
}

/** Papan harga: tiga tempat termurah yang sedang buka, harga distabilo. */
function PriceBoard({ shops }: { shops: ShopSummary[] }) {
    const cheapest = shops
        .filter(
            (shop) =>
                shop.startingPrice !== null && shop.openState !== 'closed',
        )
        .sort((a, b) => (a.startingPrice ?? 0) - (b.startingPrice ?? 0))
        .slice(0, 3);

    return (
        <div className="rounded-lg bg-card text-card-foreground shadow-xl shadow-black/20">
            <p className="border-b px-5 py-3 font-display text-sm font-semibold">
                Paling murah sekarang
            </p>
            <ul className="divide-y">
                {cheapest.map((shop, index) => (
                    <li
                        key={shop.id}
                        style={{ animationDelay: `${300 + index * 120}ms` }}
                        className="flex items-center justify-between gap-4 px-5 py-4 motion-safe:animate-in motion-safe:duration-500 motion-safe:fill-mode-backwards motion-safe:fade-in motion-safe:slide-in-from-bottom-2"
                    >
                        <div className="min-w-0">
                            <p className="truncate font-display font-semibold">
                                {shop.name}
                            </p>
                            <div className="mt-1">
                                <OpenStatusBadge state={shop.openState} />
                            </div>
                        </div>
                        <p className="shrink-0 rounded-sm bg-highlight px-2 py-0.5 font-display text-xl font-bold text-highlight-foreground tabular-nums">
                            {formatRupiah(shop.startingPrice ?? 0)}
                        </p>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export function HomeHero({ shops, openCount }: Props) {
    const prices = shops
        .map((shop) => shop.startingPrice)
        .filter((price): price is number => price !== null);
    const lowest = prices.length > 0 ? Math.min(...prices) : null;

    return (
        <section className="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden bg-primary text-primary-foreground">
            <CropMarks />
            <div className="relative mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:py-16 lg:grid-cols-[1.15fr_1fr]">
                <div className="flex flex-col gap-6">
                    <h1 className="font-display text-4xl leading-[1.05] font-extrabold tracking-tight sm:text-6xl">
                        Cetak di CitraLand tanpa keliling
                    </h1>
                    <p className="max-w-lg text-lg text-primary-foreground/80">
                        Lihat tempat printing yang masih buka, bandingkan harga
                        dan jaraknya dari kamu, lalu hubungi lewat WhatsApp.
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <Button
                            asChild
                            size="lg"
                            className="bg-highlight text-highlight-foreground hover:bg-highlight/90"
                        >
                            <a href="#daftar">Cari tempat terdekat</a>
                        </Button>
                        <Button
                            asChild
                            size="lg"
                            variant="outline"
                            className="border-white/30 bg-transparent text-white hover:bg-white/10 hover:text-white"
                        >
                            <Link href={agentIndex().url}>
                                Tanya asisten cetak
                            </Link>
                        </Button>
                    </div>
                    <dl className="grid grid-cols-3 gap-4 border-t border-white/15 pt-6">
                        <Stat value={String(shops.length)} label="Tempat" />
                        <Stat value={String(openCount)} label="Buka sekarang" />
                        <Stat
                            value={lowest === null ? '–' : formatRupiah(lowest)}
                            label="Harga mulai dari"
                        />
                    </dl>
                </div>
                <PriceBoard shops={shops} />
            </div>
        </section>
    );
}
