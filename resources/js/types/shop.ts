export type OpenState = 'open' | 'closing_soon' | 'closed';

export type PrintPrice = {
    price: number;
    source: 'verified' | 'estimated';
};

export type ShopSummary = {
    id: number;
    slug: string;
    name: string;
    address: string;
    latitude: number;
    longitude: number;
    whatsapp: string | null;
    openState: OpenState;
    distanceKm: number | null;
    startingPrice: number | null;
    /** Print termurah per pilihan "{kertas}_{warna}", dengan "any" untuk yang tidak dipilih. */
    printPrices: Partial<Record<string, PrintPrice>>;
    services: string[];
};

export type ShopPrice = {
    service: string;
    paperSize: string | null;
    colorMode: string | null;
    price: number;
    unit: string;
    source: 'verified' | 'estimated';
};

export type ShopDetail = {
    id: number;
    slug: string;
    name: string;
    address: string;
    latitude: number;
    longitude: number;
    whatsapp: string | null;
    openState: OpenState;
    closesAt: string | null;
    opensAt: string | null;
    openingHours: Record<string, { open: string; close: string }[]>;
    services: string[];
    startingPrice: number | null;
    prices: ShopPrice[];
    updatedAt: string | null;
};
