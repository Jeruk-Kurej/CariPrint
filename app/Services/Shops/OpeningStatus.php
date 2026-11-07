<?php

namespace App\Services\Shops;

use App\Enums\OpenState;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Works out whether a shop is open right now, from its surveyed opening hours.
 * Pure PHP on purpose: AI never decides open status (see guidelines, core rule 1).
 */
class OpeningStatus
{
    private const DAYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    /**
     * @param  array<string, list<array{open: string, close: string}>>  $openingHours
     */
    public function evaluate(array $openingHours, CarbonImmutable $now): OpeningResult
    {
        $intervals = $this->intervalsAround($openingHours, $now);

        $current = $intervals->first(fn (array $i) => $now >= $i['start'] && $now < $i['end']);

        if ($current === null) {
            return new OpeningResult(OpenState::Closed, opensAt: $this->nextOpening($openingHours, $now));
        }

        $closingSoon = ! $current['always_open']
            && $now->diffInMinutes($current['end']) <= config('cariprint.directory.closing_soon_minutes');

        return new OpeningResult(
            $closingSoon ? OpenState::ClosingSoon : OpenState::Open,
            closesAt: $current['end'],
        );
    }

    /**
     * Intervals from yesterday (to catch overnight hours) through tomorrow, as absolute times.
     *
     * @param  array<string, list<array{open: string, close: string}>>  $openingHours
     * @return Collection<int, array{start: CarbonImmutable, end: CarbonImmutable, always_open: bool}>
     */
    private function intervalsAround(array $openingHours, CarbonImmutable $now): Collection
    {
        return collect([-1, 0, 1])
            ->flatMap(fn (int $offset) => $this->intervalsOn($openingHours, $now->startOfDay()->addDays($offset)));
    }

    /**
     * @param  array<string, list<array{open: string, close: string}>>  $openingHours
     * @return Collection<int, array{start: CarbonImmutable, end: CarbonImmutable, always_open: bool}>
     */
    private function intervalsOn(array $openingHours, CarbonImmutable $day): Collection
    {
        $key = self::DAYS[$day->dayOfWeekIso - 1];

        return collect($openingHours[$key] ?? [])->map(function (array $slot) use ($day) {
            $start = $day->setTimeFromTimeString($slot['open']);
            $alwaysOpen = $slot['close'] === '24:00';
            $end = $alwaysOpen
                ? $day->addDay()->startOfDay()
                : $day->setTimeFromTimeString($slot['close']);

            // Closing time before opening time means the shop closes after midnight.
            if ($end <= $start) {
                $end = $end->addDay();
            }

            return ['start' => $start, 'end' => $end, 'always_open' => $alwaysOpen];
        });
    }

    /**
     * @param  array<string, list<array{open: string, close: string}>>  $openingHours
     */
    private function nextOpening(array $openingHours, CarbonImmutable $now): ?CarbonImmutable
    {
        return collect(range(0, 7))
            ->flatMap(fn (int $offset) => $this->intervalsOn($openingHours, $now->startOfDay()->addDays($offset)))
            ->map(fn (array $i) => $i['start'])
            ->first(fn (CarbonImmutable $start) => $start > $now);
    }
}
