import { MapPin } from 'lucide-react';

/** Shown instead of the map when no Google Maps key is configured. The list still works. */
export function MapPlaceholder() {
    return (
        <div className="flex h-72 flex-col items-center justify-center gap-2 rounded-xl border border-dashed bg-muted/40 p-6 text-center sm:h-full sm:min-h-96">
            <MapPin className="size-8 text-muted-foreground" aria-hidden />
            <p className="font-medium">Peta belum aktif</p>
            <p className="max-w-xs text-sm text-muted-foreground">
                Daftar di samping tetap bisa dipakai. Peta akan muncul setelah
                kunci Google Maps dipasang.
            </p>
        </div>
    );
}
