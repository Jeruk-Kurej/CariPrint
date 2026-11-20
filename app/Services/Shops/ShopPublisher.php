<?php

namespace App\Services\Shops;

use App\Enums\ShopStatus;
use App\Models\Shop;
use Illuminate\Support\Collection;

/**
 * Publishes drafts, but not a shop without opening hours: it would show as closed all week, which is wrong data.
 */
class ShopPublisher
{
    /**
     * @param  Collection<int, Shop>  $shops
     * @return array{published: int, withoutHours: int}
     */
    public function publish(Collection $shops): array
    {
        [$ready, $withoutHours] = $shops->partition(fn (Shop $shop) => $shop->hasOpeningHours());

        $ready->each(fn (Shop $shop) => $shop->update(['status' => ShopStatus::Published]));

        return ['published' => $ready->count(), 'withoutHours' => $withoutHours->count()];
    }
}
