import { Search } from 'lucide-react';

/** Kolom pencarian nama tempat, sama di halaman Cari tempat dan Bandingkan. */
export function SearchField({
    value,
    onChange,
}: {
    value: string;
    onChange: (value: string) => void;
}) {
    return (
        <div className="relative flex-1 sm:max-w-xs">
            <Search
                aria-hidden
                className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
            />
            <input
                type="search"
                value={value}
                onChange={(event) => onChange(event.target.value)}
                placeholder="Cari nama tempat..."
                aria-label="Cari nama tempat"
                className="w-full rounded-full border-2 border-foreground/15 bg-background py-1.5 pr-3 pl-9 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
            />
        </div>
    );
}
