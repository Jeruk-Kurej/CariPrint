export type OpenState = 'open' | 'closing_soon' | 'closed';

export type ShopSummary = {
    id: number;
    slug: string;
    name: string;
    address: string;
    whatsapp: string | null;
    openState: OpenState;
    distanceKm: number | null;
    startingPrice: number | null;
};
