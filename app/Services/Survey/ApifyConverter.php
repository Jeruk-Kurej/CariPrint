<?php

namespace App\Services\Survey;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Turns a Google Maps export from Apify into rows in the team's survey format (docs/SURVEY.md).
 * Prices are never in that export, so they stay empty until the shop owner confirms them.
 */
class ApifyConverter
{
    private const DAYS = [
        'mon' => 'Senin', 'tue' => 'Selasa', 'wed' => 'Rabu', 'thu' => 'Kamis',
        'fri' => 'Jumat', 'sat' => 'Sabtu', 'sun' => 'Minggu',
    ];

    private const RELEVANT = '/percetakan|cetak digital|fotokopi|fotocopy|print/i';

    private const NOT_RELEVANT = '/3d|tinta|alat tulis|bank/i';

    /**
     * @param  array{min_latitude: float, max_latitude: float, min_longitude: float, max_longitude: float}  $area
     * @return array{shops: list<array<string, string>>, needsCheck: list<array<string, string>>, skipped: list<array{title: string, reason: string}>}
     */
    public function convert(string $csvPath, array $area): array
    {
        $shops = [];
        $needsCheck = [];
        $skipped = [];
        $slugs = [];

        foreach ($this->rows($csvPath) as $row) {
            $title = trim($row['title'] ?? '');

            if (($reason = $this->skipReason($row, $area)) !== null) {
                $skipped[] = ['title' => $title, 'reason' => $reason];

                continue;
            }

            $converted = $this->shop($row, $this->uniqueSlug($title, $slugs));

            if ($this->hasNoHours($converted)) {
                $needsCheck[] = $converted;

                continue;
            }

            $shops[] = $converted;
        }

        return ['shops' => $shops, 'needsCheck' => $needsCheck, 'skipped' => $skipped];
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<string, float>  $area
     */
    private function skipReason(array $row, array $area): ?string
    {
        $categories = $this->categories($row);

        if (! preg_match(self::RELEVANT, $categories) || preg_match(self::NOT_RELEVANT, $categories)) {
            return 'bukan tempat print atau fotokopi';
        }

        if (($row['permanentlyClosed'] ?? '') === 'true') {
            return 'tutup permanen';
        }

        $lat = (float) ($row['location/lat'] ?? 0);
        $lng = (float) ($row['location/lng'] ?? 0);

        if ($lat < $area['min_latitude'] || $lat > $area['max_latitude'] || $lng < $area['min_longitude'] || $lng > $area['max_longitude']) {
            return 'di luar area CitraLand';
        }

        return null;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function categories(array $row): string
    {
        $names = [$row['categoryName'] ?? ''];

        foreach (range(0, 6) as $i) {
            $names[] = $row["categories/$i"] ?? '';
        }

        return implode(' | ', array_filter($names));
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, string>
     */
    private function shop(array $row, string $slug): array
    {
        $phone = ltrim($row['phoneUnformatted'] ?? '', '+');
        $categories = $this->categories($row);
        $shop = [
            'slug' => $slug,
            'name' => trim($row['title']),
            'address' => trim($row['address'] ?? ''),
            'latitude' => $row['location/lat'] ?? '',
            'longitude' => $row['location/lng'] ?? '',
            'whatsapp' => str_starts_with($phone, '628') ? $phone : '',
            'place_id' => $row['placeId'] ?? '',
        ];

        foreach (self::DAYS as $key => $label) {
            $shop[$key] = $this->hoursFor($row, $label);
        }

        return $shop + [
            'services' => preg_match('/fotokopi|fotocopy/i', $categories) ? 'print|photocopy' : 'print',
            'color_page_rule' => '',
            'surveyed_at' => substr($row['scrapedAt'] ?? now()->toIso8601String(), 0, 10),
            'surveyor' => 'Apify Google Maps',
            'phone' => $row['phone'] ?? '',
        ];
    }

    /**
     * @param  array<string, string>  $row
     */
    private function hoursFor(array $row, string $dayLabel): string
    {
        foreach (range(0, 6) as $i) {
            if (($row["openingHours/$i/day"] ?? '') === $dayLabel) {
                return $this->parseHours($row["openingHours/$i/hours"] ?? '');
            }
        }

        return '';
    }

    /**
     * Google writes "08.00 to 18.00", "08.00 to 11.00, 15.00 to 21.00", "Tutup" or "Buka 24 jam".
     */
    private function parseHours(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        if (strcasecmp($text, 'Tutup') === 0) {
            return 'tutup';
        }

        if (preg_match('/24\s*jam/i', $text)) {
            return '00:00-24:00';
        }

        $slots = [];

        foreach (explode(',', $text) as $part) {
            if (! preg_match('/(\d{1,2})[.:](\d{2})\s*(?:to|–|-)\s*(\d{1,2})[.:](\d{2})/u', $part, $m)) {
                throw new InvalidArgumentException("Format jam tidak dikenal: $text");
            }

            $slots[] = sprintf('%02d:%s-%02d:%s', $m[1], $m[2], $m[3], $m[4]);
        }

        return implode('|', $slots);
    }

    /**
     * @param  array<string, string>  $shop
     */
    private function hasNoHours(array $shop): bool
    {
        foreach (array_keys(self::DAYS) as $day) {
            if ($shop[$day] !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, true>  $slugs
     */
    private function uniqueSlug(string $title, array &$slugs): string
    {
        $base = Str::slug($title) ?: 'tempat';
        $slug = $base;

        for ($i = 2; isset($slugs[$slug]); $i++) {
            $slug = "$base-$i";
        }

        $slugs[$slug] = true;

        return $slug;
    }

    /**
     * @return list<array<string, string>>
     */
    private function rows(string $path): array
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("File Apify tidak ditemukan: $path");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null] || $line === []) {
                continue;
            }
            $rows[] = array_combine($header, array_pad($line, count($header), ''));
        }

        fclose($handle);

        return $rows;
    }
}
