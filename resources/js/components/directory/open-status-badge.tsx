import type { OpenState } from '@/types';

const labels: Record<OpenState, string> = {
    open: 'Buka',
    closing_soon: 'Segera tutup',
    closed: 'Tutup',
};

const styles: Record<OpenState, string> = {
    open: 'bg-card text-status-open ring-1 ring-status-open/40',
    closing_soon: 'bg-card text-status-closing ring-1 ring-status-closing/40',
    closed: 'bg-card/70 text-muted-foreground ring-1 ring-foreground/20',
};

/** Status buka sebagai label yang mudah terlihat, warnanya dari token di app.css. */
export function OpenStatusBadge({ state }: { state: OpenState }) {
    return (
        <span
            className={`inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-sm font-bold ${styles[state]}`}
        >
            <span className="size-2 rounded-full bg-current" aria-hidden />
            {labels[state]}
        </span>
    );
}
