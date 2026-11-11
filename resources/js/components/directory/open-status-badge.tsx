import { Badge } from '@/components/ui/badge';
import type { OpenState } from '@/types';

const labels: Record<OpenState, string> = {
    open: 'Buka',
    closing_soon: 'Segera tutup',
    closed: 'Tutup',
};

const styles: Record<OpenState, string> = {
    open: 'border-transparent bg-status-open text-white',
    closing_soon: 'border-transparent bg-status-closing text-foreground',
    closed: 'border-transparent bg-status-closed text-white',
};

/** Status buka: warna dari token di app.css, supaya selalu sama di semua halaman. */
export function OpenStatusBadge({ state }: { state: OpenState }) {
    return (
        <Badge className={`px-2.5 py-1 text-xs font-semibold ${styles[state]}`}>
            {labels[state]}
        </Badge>
    );
}
