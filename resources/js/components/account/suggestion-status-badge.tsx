const tones: Record<string, string> = {
    pending: 'bg-note-yellow text-foreground',
    accepted: 'bg-note-mint text-status-open',
    rejected: 'bg-note-pink text-destructive',
};

/** Status usulan tempat sebagai stiker kecil: kuning menunggu, hijau diterima, merah muda ditolak. */
export function SuggestionStatusBadge({
    status,
    label,
}: {
    status: string;
    label: string;
}) {
    return (
        <span
            className={`inline-flex shrink-0 self-start rounded-full border border-foreground/15 px-3 py-1 text-sm font-bold ${tones[status] ?? tones.pending}`}
        >
            {label}
        </span>
    );
}
