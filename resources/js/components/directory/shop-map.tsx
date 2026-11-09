import { APIProvider, Map, Marker } from '@vis.gl/react-google-maps';
import { CITRALAND_CENTER } from '@/lib/map';
import type { OpenState, ShopSummary } from '@/types';

const markerColor: Record<OpenState, string> = {
    open: '#16a34a',
    closing_soon: '#ca8a04',
    closed: '#dc2626',
};

type Props = {
    shops: ShopSummary[];
    selectedId: number | null;
    onSelect: (id: number) => void;
};

export function ShopMap({ shops, selectedId, onSelect }: Props) {
    const selected = shops.find((shop) => shop.id === selectedId);
    const center = selected
        ? { lat: selected.latitude, lng: selected.longitude }
        : CITRALAND_CENTER;

    return (
        <APIProvider apiKey={import.meta.env.VITE_GOOGLE_MAPS_API_KEY ?? ''}>
            <div className="h-72 w-full overflow-hidden rounded-xl border sm:h-full sm:min-h-96">
                <Map
                    center={center}
                    zoom={selected ? 16 : 15}
                    gestureHandling="greedy"
                    disableDefaultUI
                    style={{ width: '100%', height: '100%' }}
                >
                    {shops.map((shop) => (
                        <Marker
                            key={shop.id}
                            position={{
                                lat: shop.latitude,
                                lng: shop.longitude,
                            }}
                            title={shop.name}
                            onClick={() => onSelect(shop.id)}
                            icon={{
                                path: 'M 0,0 m -8,0 a 8,8 0 1,0 16,0 a 8,8 0 1,0 -16,0',
                                fillColor: markerColor[shop.openState],
                                fillOpacity: 1,
                                strokeColor: '#ffffff',
                                strokeWeight: shop.id === selectedId ? 4 : 2,
                                scale: 1,
                            }}
                        />
                    ))}
                </Map>
            </div>
        </APIProvider>
    );
}
