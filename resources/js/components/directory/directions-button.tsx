import { Navigation } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { directionsUrl } from '@/lib/maps';

type Props = {
    latitude: number;
    longitude: number;
    variant?: 'default' | 'outline';
};

/** Tombol "Rute" di halaman detail. Membuka Google Maps di tab baru, jadi tujuannya ditulis di nama aksesibelnya. */
export function DirectionsButton({
    latitude,
    longitude,
    variant = 'default',
}: Props) {
    return (
        <Button asChild size="sm" variant={variant} className="rounded-full">
            <a
                href={directionsUrl(latitude, longitude)}
                target="_blank"
                rel="noopener noreferrer"
            >
                <Navigation aria-hidden />
                Rute
                <span className="sr-only"> di Google Maps (tab baru)</span>
            </a>
        </Button>
    );
}
