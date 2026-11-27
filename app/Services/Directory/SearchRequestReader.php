<?php

namespace App\Services\Directory;

use App\Ai\Agents\ShopSearchAgent;
use App\Enums\ColorMode;
use App\Enums\PaperSize;
use Illuminate\Support\Str;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Throwable;

/**
 * Turns the AI's reading of a sentence into the filters the directory page already understands.
 * Anything outside the fixed choices is dropped, so the AI can never produce a query of its own.
 */
class SearchRequestReader
{
    private const SERVICE_GROUPS = ['print', 'photocopy', 'binding', 'scan', 'laminating'];

    private const SORTS = ['price', 'distance'];

    private const DISTANCES_KM = [1, 2, 5];

    private const TIMEOUT_SECONDS = 20;

    /**
     * @throws SearchAssistantUnavailable
     */
    public function read(string $sentence): SearchFilters
    {
        $parsed = $this->ask($sentence);
        $maxKm = $parsed['max_km'] ?? null;

        return new SearchFilters(
            area: $this->words($parsed['area'] ?? null),
            shopName: $this->words($parsed['shop_name'] ?? null),
            paper: PaperSize::tryFrom((string) ($parsed['paper_size'] ?? '')),
            color: ColorMode::tryFrom((string) ($parsed['color_mode'] ?? '')),
            services: array_values(array_intersect(self::SERVICE_GROUPS, (array) ($parsed['services'] ?? []))),
            sort: in_array($parsed['sort'] ?? null, self::SORTS, true) ? $parsed['sort'] : null,
            openOnly: ($parsed['open_only'] ?? false) === true,
            maxKm: in_array($maxKm, self::DISTANCES_KM, true) ? $maxKm : null,
        );
    }

    /**
     * @throws SearchAssistantUnavailable
     */
    private function ask(string $sentence): StructuredAgentResponse
    {
        try {
            return (new ShopSearchAgent)->prompt($sentence, timeout: self::TIMEOUT_SECONDS);
        } catch (Throwable $exception) {
            report($exception);

            throw new SearchAssistantUnavailable;
        }
    }

    private function words(mixed $value): string
    {
        return is_string($value) ? Str::of($value)->squish()->limit(60, '')->toString() : '';
    }
}
