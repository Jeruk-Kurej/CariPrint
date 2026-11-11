<?php

namespace App\Services\Directory;

use App\Models\PriceList;
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

        /** @var Collection<int, array<string, mixed>> $cards */
        $cards = Shop::query()->with('priceLists')->orderBy('name')->get()->map(function (Shop $shop) use ($now, $latitude, $longitude) {
            return [
                'id' => $shop->id,
                'slug' => $shop->slug,
                'name' => $shop->name,
                'address' => $shop->address,
                'latitude' => $shop->latitude,
                'longitude' => $shop->longitude,
                'whatsapp' => $shop->whatsapp,
                'openState' => $this->openingStatus->evaluate($shop->opening_hours, $now)->state->value,
                'distanceKm' => $latitude === null || $longitude === null
                    ? null
                    : round($this->distance->kilometers($latitude, $longitude, $shop->latitude, $shop->longitude), 2),
                'startingPrice' => $shop->priceLists->min('price'),
                'services' => $shop->services->map(fn ($service) => $service->name)->values(),
            ];
        });

        return $latitude === null || $longitude === null
            ? $cards
            : $cards->sortBy('distanceKm')->values();
    }

    private function localTime(?CarbonImmutable $time): ?string
    {
        $localized = $time?->locale('id');

        return $localized instanceof CarbonImmutable ? $localized->translatedFormat('l H:i') : null;
    }

    /**
     * Full detail for the shop page: hours, services, prices with their source, and when the data was last updated.
     *
     * @return array<string, mixed>
     */
    public function detail(Shop $shop): array
    {
        $now = CarbonImmutable::now('Asia/Jakarta');
        $status = $this->openingStatus->evaluate($shop->opening_hours, $now);

        return [
            'id' => $shop->id,
            'name' => $shop->name,
            'slug' => $shop->slug,
            'address' => $shop->address,
            'latitude' => $shop->latitude,
            'longitude' => $shop->longitude,
            'whatsapp' => $shop->whatsapp,
            'openState' => $status->state->value,
            'closesAt' => $status->closesAt?->format('H:i'),
            'opensAt' => $this->localTime($status->opensAt),
            'openingHours' => $shop->opening_hours,
            'services' => $shop->services->map(fn ($service) => $service->name)->values(),
            'prices' => $shop->priceLists->map(fn (PriceList $p) => [
                'service' => $p->service->name,
                'paperSize' => $p->paper_size?->value,
                'colorMode' => $p->color_mode?->value,
                'price' => $p->price,
                'unit' => $p->unit->value,
                'source' => $p->source->value,
            ])->values(),
            'updatedAt' => $shop->updated_at?->format('d M Y'),
        ];
    }
}
