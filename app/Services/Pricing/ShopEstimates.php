<?php

namespace App\Services\Pricing;

use App\Models\Shop;
use App\Services\Location\DistanceService;
use App\Services\Shops\OpeningStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Prices one print spec at every shop for the print assistant. Totals, the cheapest flag, open status and
 * distance are all worked out here in PHP, never by AI (core rule 1).
 */
class ShopEstimates
{
    public function __construct(
        private readonly PriceCalculator $calculator,
        private readonly OpeningStatus $openingStatus,
        private readonly DistanceService $distance,
    ) {}

    /**
     * Published shops that have no price list yet, so they cannot be part of an estimate.
     */
    public function withoutPrices(): int
    {
        return Shop::published()->doesntHave('priceLists')->count();
    }

    /**
     * Cheapest first; shops that cannot price the request go last. Coordinates are used for this one
     * calculation only and are never stored or logged.
     *
     * @param  array{0: float, 1: float}|null  $coordinates
     * @return Collection<int, array<string, mixed>>
     */
    public function forSpec(PrintSpec $spec, ?array $coordinates = null): Collection
    {
        $now = CarbonImmutable::now('Asia/Jakarta');

        /** @var Collection<int, array<string, mixed>> $rows */
        $rows = Shop::published()->has('priceLists')->with('priceLists')->orderBy('name')->get()
            ->map(function (Shop $shop) use ($spec, $coordinates, $now) {
                $breakdown = $this->calculator->forShop($shop, $spec);

                return [
                    'name' => $shop->name,
                    'slug' => $shop->slug,
                    'openState' => $this->openingStatus->evaluate($shop->opening_hours, $now)->state->value,
                    'todayHours' => $this->openingStatus->todayHours($shop->opening_hours, $now),
                    'distanceKm' => $coordinates === null
                        ? null
                        : round($this->distance->kilometers($coordinates[0], $coordinates[1], $shop->latitude, $shop->longitude), 2),
                    'total' => $breakdown->total(),
                    'missing' => $breakdown->missing,
                ];
            })
            ->sortBy(fn (array $row) => $row['total'] ?? PHP_INT_MAX)
            ->values();

        $lowest = $rows->pluck('total')->reject(fn (mixed $total) => $total === null)->min();

        /** @var Collection<int, array<string, mixed>> $flagged */
        $flagged = $rows->map(fn (array $row) => [...$row, 'cheapest' => $row['total'] !== null && $row['total'] === $lowest]);

        return $flagged;
    }
}
