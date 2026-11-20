<?php

namespace App\Services\Survey;

use App\Enums\ShopStatus;
use App\Models\Shop;
use Illuminate\Support\Str;

/**
 * Turns an Apify Google Maps export into draft shops for an admin to check. It only adds: a shop that is already
 * in the database (same place_id) is skipped, never overwritten, so checked data cannot be damaged by a re-import.
 */
class ApifyDraftImporter
{
    public function __construct(
        private ApifyConverter $converter,
        private SurveyImporter $survey,
    ) {}

    /**
     * Imports several exports in a row and adds their results together, so one summary covers them all.
     *
     * @param  list<string>  $csvPaths
     * @return array{created: int, existing: int, withoutHours: int, skipped: array<string, int>}
     */
    public function importAll(array $csvPaths, SurveyArea $area): array
    {
        $total = ['created' => 0, 'existing' => 0, 'withoutHours' => 0, 'skipped' => []];

        foreach ($csvPaths as $path) {
            $summary = $this->import($path, $area);

            foreach (['created', 'existing', 'withoutHours'] as $key) {
                $total[$key] += $summary[$key];
            }

            foreach ($summary['skipped'] as $reason => $count) {
                $total['skipped'][$reason] = ($total['skipped'][$reason] ?? 0) + $count;
            }
        }

        return $total;
    }

    /**
     * @return array{created: int, existing: int, withoutHours: int, skipped: array<string, int>}
     */
    public function import(string $csvPath, SurveyArea $area): array
    {
        $result = $this->converter->convert($csvPath, $area);
        $needsHours = array_flip(array_column($result['needsCheck'], 'slug'));
        $created = 0;
        $withoutHours = 0;
        $existing = 0;
        $seen = [];

        foreach ([...$result['shops'], ...$result['needsCheck']] as $row) {
            $placeId = $row['place_id'];

            if ($placeId !== '' && (isset($seen[$placeId]) || Shop::where('place_id', $placeId)->exists())) {
                $existing++;

                continue;
            }

            $seen[$placeId] = true;
            $withoutHours += isset($needsHours[$row['slug']]) ? 1 : 0;

            Shop::create([...$this->survey->attributes($row), 'slug' => $this->freeSlug($row['slug']), 'status' => ShopStatus::Draft]);
            $created++;
        }

        return [
            'created' => $created,
            'existing' => $existing,
            'withoutHours' => $withoutHours,
            'skipped' => collect($result['skipped'])->countBy('reason')->all(),
        ];
    }

    private function freeSlug(string $slug): string
    {
        $base = Str::limit($slug, 200, '');
        $candidate = $base;

        for ($i = 2; Shop::where('slug', $candidate)->exists(); $i++) {
            $candidate = "$base-$i";
        }

        return $candidate;
    }
}
