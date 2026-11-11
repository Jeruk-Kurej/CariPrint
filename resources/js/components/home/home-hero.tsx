import { Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
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

const stackPlaces = [
    'left-0 top-0 -rotate-3',
    'right-0 top-24 rotate-2',
    'bottom-0 left-12 -rotate-1',
];

/** Pamflet termurah yang sedang buka, bertumpuk seperti tertempel di papan. */
function PinnedStack({ shops }: { shops: ShopSummary[] }) {
    return (
        <div className="relative hidden h-[34rem] lg:block" aria-hidden>
            {shops.map((shop, index) => (
                <div
                    key={shop.id}
                    className={`absolute w-72 ${stackPlaces[index]}`}
                >
                    <FlyerCard shop={shop} tilted={false} />
                </div>
            ))}
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
        <section className="grid items-center gap-10 px-4 py-10 sm:py-16 lg:grid-cols-[1.1fr_1fr]">
            <div className="flex flex-col gap-6">
                <h1 className="font-display text-5xl leading-[1.02] font-extrabold tracking-tight sm:text-7xl">
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
