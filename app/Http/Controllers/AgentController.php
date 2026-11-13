<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Models\Shop;
use App\Services\Location\DistanceService;
use App\Services\Pricing\PriceCalculator;
use App\Services\Pricing\PrintRequestReader;
use App\Services\Pricing\UnreadablePrintRequest;
use App\Services\Shops\OpeningStatus;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    private const DAYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    public function index(): Response
    {
        return Inertia::render('agent/index', $this->formProps());
    }

    public function parse(
        AgentRequest $request,
        PrintRequestReader $reader,
        PriceCalculator $calculator,
        OpeningStatus $openingStatus,
        DistanceService $distance,
    ): Response {
        try {
            $spec = $reader->read($request->string('request')->toString(), $request->documentPages(), $request->coloredPages());
        } catch (UnreadablePrintRequest $exception) {
            throw ValidationException::withMessages(['request' => $exception->getMessage()]);
        }

        $now = CarbonImmutable::now('Asia/Jakarta');
        $coordinates = $request->coordinates();
        $shops = Shop::query()->with('priceLists')->orderBy('name')->get();

        $estimates = $shops
            ->map(function (Shop $shop) use ($calculator, $spec, $openingStatus, $distance, $now, $coordinates) {
                $breakdown = $calculator->forShop($shop, $spec);
                $status = $openingStatus->evaluate($shop->opening_hours, $now);

                return [
                    'name' => $shop->name,
                    'slug' => $shop->slug,
                    'openState' => $status->state->value,
                    'todayHours' => $this->todayHours($shop->opening_hours, $now),
                    'distanceKm' => $coordinates === null
                        ? null
                        : round($distance->kilometers($coordinates[0], $coordinates[1], $shop->latitude, $shop->longitude), 2),
                    'total' => $breakdown->total(),
                    'missing' => $breakdown->missing,
                ];
            })
            ->sortBy(fn (array $row) => $row['total'] ?? PHP_INT_MAX)
            ->values();

        return Inertia::render('agent/index', [
            ...$this->formProps(),
            'spec' => [
                'paperSize' => $spec->paperSize->value,
                'bwPages' => $spec->bwPages,
                'colorPages' => $spec->colorPages,
                'bwRuns' => $spec->pages?->blackWhiteRuns() ?? [],
                'colorRuns' => $spec->pages?->colorRuns() ?? [],
                'copies' => $spec->copies,
                'binding' => $spec->binding?->value,
            ],
            'estimates' => $estimates,
        ]);
    }

    /**
     * Formats today's opening slots (e.g. "08:00–17:00") so students can double-check hours right on the estimate card.
     *
     * @param  array<string, list<array{open: string, close: string}>>  $openingHours
     */
    private function todayHours(array $openingHours, CarbonImmutable $now): string
    {
        $key = self::DAYS[$now->dayOfWeekIso - 1];
        $slots = $openingHours[$key] ?? [];

        if ($slots === []) {
            return 'Tutup hari ini';
        }

        return implode(', ', array_map(
            fn (array $slot) => $slot['open'] === '00:00' && $slot['close'] === '24:00'
                ? '24 jam'
                : "{$slot['open']}–{$slot['close']}",
            $slots,
        ));
    }

    /**
     * Limits the browser checks before it reads an attached PDF.
     *
     * @return array{fileLimits: array{maxMegabytes: int, maxPages: int}}
     */
    private function formProps(): array
    {
        return [
            'fileLimits' => [
                'maxMegabytes' => config('cariprint.uploads.max_megabytes'),
                'maxPages' => config('cariprint.uploads.max_pages'),
            ],
        ];
    }
}
