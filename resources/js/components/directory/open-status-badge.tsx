import type { OpenState } from '@/types';

const labels: Record<OpenState, string> = {
    open: 'Buka',
    closing_soon: 'Segera tutup',
    closed: 'Tutup',
};

const styles: Record<OpenState, string> = {
    open: 'bg-status-open/12 text-status-open',
    closing_soon: 'bg-status-closing/15 text-status-closing',
    closed: 'bg-muted text-muted-foreground',
};

/** Status buka berbentuk label lembut, warnanya dari token di app.css. */
export function OpenStatusBadge({ state }: { state: OpenState }) {
    return (
        <span
            className={`inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold ${styles[state]}`}
        >
            <span className="size-1.5 rounded-full bg-current" aria-hidden />
            {labels[state]}
        </span>
    );
}
