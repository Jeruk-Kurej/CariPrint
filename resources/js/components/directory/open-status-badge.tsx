import { Badge } from '@/components/ui/badge';
import type { OpenState } from '@/types';

const labels: Record<OpenState, string> = {
    open: 'Buka',
    closing_soon: 'Segera tutup',
    closed: 'Tutup',
};

const variants = {
    open: 'default',
    closing_soon: 'secondary',
    closed: 'destructive',
} as const satisfies Record<OpenState, string>;

export function OpenStatusBadge({ state }: { state: OpenState }) {
    return <Badge variant={variants[state]}>{labels[state]}</Badge>;
}
