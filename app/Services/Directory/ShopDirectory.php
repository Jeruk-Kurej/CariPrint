<?php

namespace App\Services\Directory;

use App\Models\Shop;
use App\Services\Location\DistanceService;
use App\Services\Shops\OpeningStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Builds the shop cards for the home page. Open status and distance are computed here, never by AI.
 */
class ShopDirectory
{
    public function __construct(
        private readonly OpeningStatus $openingStatus,
        private readonly DistanceService $distance,
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(?float $latitude = null, ?float $longitude = null): Collection
    {
        $now = CarbonImmutable::now('Asia/Jakarta');

        $cards = Shop::query()->with('priceLists')->orderBy('name')->get()->map(function (Shop $shop) use ($now, $latitude, $longitude) {
            return [
                'id' => $shop->id,
                'slug' => $shop->slug,
                'name' => $shop->name,
                'address' => $shop->address,
                'whatsapp' => $shop->whatsapp,
                'openState' => $this->openingStatus->evaluate($shop->opening_hours, $now)->state->value,
                'distanceKm' => $latitude === null || $longitude === null
                    ? null
                    : round($this->distance->kilometers($latitude, $longitude, $shop->latitude, $shop->longitude), 2),
                'startingPrice' => $shop->priceLists->min('price'),
            ];
        });

        return $latitude === null || $longitude === null
            ? $cards
            : $cards->sortBy('distanceKm')->values();
    }
}
