import { Badge } from '@/components/ui/badge';

/** Tells the visitor whether a price came from our survey or is only an estimate. */
export function PriceSourceBadge({
    source,
}: {
    source: 'verified' | 'estimated';
}) {
    return source === 'verified' ? (
        <Badge className="bg-price-verified text-brand-foreground">
            Terverifikasi
        </Badge>
    ) : (
        <Badge variant="outline" className="text-price-estimated">
            Perkiraan
        </Badge>
    );
}
