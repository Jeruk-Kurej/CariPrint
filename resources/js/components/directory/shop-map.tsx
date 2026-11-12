import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useEffect, useRef } from 'react';
import { CITRALAND_CENTER } from '@/lib/map';
import { markerColor, markerRingColor } from '@/lib/map-colors';
import type { ShopSummary } from '@/types';

type Props = {
    shops: ShopSummary[];
    selectedId: number | null;
    onSelect?: (id: number) => void;
    heightClass?: string;
};

/** Peta (Leaflet) dengan data OpenStreetMap, ditampilkan lewat CARTO: gratis dan tanpa API key. */
export function ShopMap({
    shops,
    selectedId,
    onSelect,
    heightClass = 'h-72 lg:h-[30rem]',
}: Props) {
    const containerRef = useRef<HTMLDivElement>(null);
    const mapRef = useRef<L.Map | null>(null);

    useEffect(() => {
        if (!containerRef.current) {
            return;
        }

        const map = L.map(containerRef.current, {
            center: [CITRALAND_CENTER.lat, CITRALAND_CENTER.lng],
            zoom: 15,
            scrollWheelZoom: false,
        });

        L.tileLayer(
            'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            {
                subdomains: 'abcd',
                detectRetina: true,
                maxZoom: 19,
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            },
        ).addTo(map);

        mapRef.current = map;

        return () => {
            map.remove();
            mapRef.current = null;
        };
    }, []);

    useEffect(() => {
        const map = mapRef.current;
        const selected = shops.find((shop) => shop.id === selectedId);

        if (map && selected) {
            map.setView([selected.latitude, selected.longitude], 16);
        }
    }, [selectedId, shops]);

    useEffect(() => {
        const map = mapRef.current;

        if (!map) {
            return;
        }

        const markers = L.layerGroup().addTo(map);

        shops.forEach((shop) => {
            const isSelected = shop.id === selectedId;

            L.circleMarker([shop.latitude, shop.longitude], {
                radius: isSelected ? 9 : 7,
                color: markerRingColor(),
                weight: isSelected ? 3 : 2,
                fillColor: markerColor(shop.openState),
                fillOpacity: 1,
            })
                .bindTooltip(shop.name)
                .on('click', () => onSelect?.(shop.id))
                .addTo(markers);
        });

        return () => {
            markers.remove();
        };
    }, [shops, selectedId, onSelect]);

    return (
        <div
            className={`relative isolate w-full overflow-hidden rounded-lg border ${heightClass}`}
        >
            <div ref={containerRef} className="absolute inset-0" />
        </div>
    );
}
