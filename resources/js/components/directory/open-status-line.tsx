import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import type { ShopDetail } from '@/types';

/** Status buka dan kapan berubah, ditaruh paling atas karena itu yang dicari lebih dulu. */
export function OpenStatusLine({
    shop,
}: {
    shop: Pick<ShopDetail, 'openState' | 'opensAt' | 'closesAt'>;
}) {
    return (
        <div className="flex flex-wrap items-center gap-2 text-sm">
            <OpenStatusBadge state={shop.openState} />
            {shop.openState === 'closed' && shop.opensAt && (
                <span>Buka lagi {shop.opensAt}</span>
            )}
            {shop.openState !== 'closed' && shop.closesAt && (
                <span>Tutup pukul {shop.closesAt}</span>
            )}
        </div>
    );
}
