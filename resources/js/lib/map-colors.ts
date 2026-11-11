import type { OpenState } from '@/types';

const statusToken: Record<OpenState, string> = {
    open: '--status-open',
    closing_soon: '--status-closing',
    closed: '--status-closed',
};

function token(name: string): string {
    return getComputedStyle(document.documentElement)
        .getPropertyValue(name)
        .trim();
}

/** Peta (Leaflet) butuh warna berupa teks, jadi dibaca dari token CSS supaya tetap satu sumber. */
export function markerColor(state: OpenState): string {
    return token(statusToken[state]);
}

export function markerRingColor(): string {
    return token('--card');
}
