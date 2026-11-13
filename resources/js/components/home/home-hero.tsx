import { Link, router } from '@inertiajs/react';
import { Paperclip, Search } from 'lucide-react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { FlyerCard } from '@/components/directory/flyer-card';
import { Button } from '@/components/ui/button';
import { index as directoryIndex } from '@/routes/directory';
import type { ShopSummary } from '@/types';

const chips = [
    { label: 'Masih buka', query: { buka: 1 } },
    { label: 'Termurah', query: { urut: 'price' } },
    { label: 'Terdekat', query: { urut: 'distance' } },
];

/** Pamflet termurah yang sedang buka: 2 di atas dan 1 di tengah bawah supaya seimbang dan tidak menutupi harga. */
function PinnedStack({ shops }: { shops: ShopSummary[] }) {
    const [first, second, third] = shops;

    return (
        <div className="relative hidden lg:block">
            <div
                aria-hidden
                className="absolute inset-x-4 inset-y-6 -rotate-2 rounded-[2.5rem] bg-highlight/70"
            />
            <div className="relative grid grid-cols-2 items-start gap-6">
                {first && (
                    <div
                        style={{ animationDelay: '300ms' }}
                        className="-rotate-2 motion-safe:animate-in motion-safe:duration-700 motion-safe:fill-mode-backwards motion-safe:fade-in motion-safe:slide-in-from-top-8"
                    >
                        <FlyerCard shop={first} tilted={false} />
                    </div>
                )}
                {second && (
                    <div
                        style={{ animationDelay: '460ms' }}
                        className="relative mt-6 rotate-2 motion-safe:animate-in motion-safe:duration-700 motion-safe:fill-mode-backwards motion-safe:fade-in motion-safe:slide-in-from-top-8"
                    >
                        <Paperclip
                            aria-hidden
                            className="absolute -top-4 right-5 z-20 size-11 rotate-[20deg] text-foreground/70"
                        />
                        <FlyerCard shop={second} tilted={false} />
                    </div>
                )}
                {third && (
                    <div
                        style={{ animationDelay: '620ms' }}
                        className="col-span-2 mx-auto w-[calc(50%-0.75rem)] -rotate-1 motion-safe:animate-in motion-safe:duration-700 motion-safe:fill-mode-backwards motion-safe:fade-in motion-safe:slide-in-from-top-8"
                    >
                        <FlyerCard shop={third} tilted={false} />
                    </div>
                )}
            </div>
        </div>
    );
}

export function HomeHero({
    shops,
    featured,
}: {
    shops: ShopSummary[];
    featured: ShopSummary[];
}) {
    const [query, setQuery] = useState('');
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    function search(event: FormEvent) {
        event.preventDefault();
        router.get(
            directoryIndex().url,
            query.trim() ? { q: query.trim() } : {},
        );
    }

    return (
        <section className="grid items-center gap-10 px-4 py-10 sm:py-16 lg:grid-cols-[1.05fr_1.05fr]">
            <div className="flex flex-col gap-6">
                <h1 className="font-display text-5xl leading-[1.02] font-extrabold tracking-tight sm:text-7xl xl:text-[5.5rem]">
                    Mau print? Cari yang buka dekat kampus.
                </h1>
                <p className="max-w-md text-lg text-muted-foreground">
                    Bandingkan harga dan jarak dulu, baru jalan. Tanpa keliling
                    dari satu tempat ke tempat lain.
                </p>
                <form
                    onSubmit={search}
                    role="search"
                    className="flex gap-2 rounded-full border-2 border-foreground bg-card p-1.5 shadow-[4px_4px_0_var(--foreground)]"
                >
                    <label htmlFor="cari-tempat" className="sr-only">
                        Cari nama tempat
                    </label>
                    <input
                        id="cari-tempat"
                        type="search"
                        value={query}
                        onChange={(event) => setQuery(event.target.value)}
                        placeholder="Cari nama tempat print"
                        className="min-w-0 flex-1 bg-transparent px-4 text-base outline-none"
                    />
                    <Button type="submit" size="lg" className="rounded-full">
                        <Search aria-hidden />
                        Cari
                    </Button>
                </form>
                <ul className="flex flex-wrap items-center gap-2">
                    {chips.map((chip) => (
                        <li key={chip.label}>
                            <Link
                                href={directoryIndex({ query: chip.query }).url}
                                className="inline-flex rounded-full border-2 border-foreground/15 bg-card px-4 py-1.5 text-sm font-semibold hover:border-primary hover:text-primary"
                            >
                                {chip.label}
                            </Link>
                        </li>
                    ))}
                </ul>
                <p className="text-sm text-muted-foreground">
                    <span className="font-display text-lg font-extrabold text-foreground tabular-nums">
                        {openCount}
                    </span>{' '}
                    dari {shops.length} tempat masih buka sekarang.
                </p>
            </div>
            <PinnedStack shops={featured} />
        </section>
    );
}
