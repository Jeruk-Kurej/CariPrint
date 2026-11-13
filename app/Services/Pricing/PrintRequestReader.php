<?php

namespace App\Services\Pricing;

use App\Ai\Agents\PrintRequestAgent;
use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\ShopService;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Throwable;

/**
 * Turns the user's sentence into a PrintSpec. The AI only reads what is asked for: pages are counted by
 * PageSelector and prices come from PriceCalculator (core rule 1).
 */
class PrintRequestReader
{
    public function __construct(private PageSelector $selector) {}

    /**
     * @param  int|null  $documentPages  Length of the attached PDF as counted in the browser, or null without a file.
     *
     * @throws UnreadablePrintRequest
     */
    public function read(string $request, ?int $documentPages): PrintSpec
    {
        $parsed = $this->ask($request, $documentPages !== null);
        $ranges = $this->ranges($parsed['page_ranges'] ?? []);
        $pages = $ranges === []
            ? null
            : $this->selector->select($ranges, $documentPages ?? $this->statedLength($parsed));

        return new PrintSpec(
            paperSize: PaperSize::from($parsed['paper_size']),
            bwPages: $pages === null ? (int) $parsed['bw_pages'] : count($pages->blackWhite),
            colorPages: $pages === null ? (int) $parsed['color_pages'] : count($pages->color),
            copies: (int) $parsed['copies'],
            binding: ShopService::tryFrom((string) ($parsed['binding'] ?? '')),
            pages: $pages,
        );
    }

    /**
     * The AI is told whether a document is attached, but never how long it is, so it cannot do the counting itself.
     *
     * @throws UnreadablePrintRequest
     */
    private function ask(string $request, bool $hasDocument): StructuredAgentResponse
    {
        $note = $hasDocument
            ? 'A PDF document is attached. If the user does not say which pages, they want all of it.'
            : 'No document is attached.';

        try {
            return (new PrintRequestAgent)->prompt("{$request}\n\n[{$note}]");
        } catch (Throwable $exception) {
            report($exception);

            throw UnreadablePrintRequest::assistantUnavailable();
        }
    }

    /**
     * @param  list<array{from: int, to: int, mode: string}>  $ranges
     * @return list<PageRange>
     */
    private function ranges(array $ranges): array
    {
        return array_map(
            fn (array $range) => new PageRange((int) $range['from'], (int) $range['to'], ColorMode::tryFrom($range['mode'])),
            $ranges,
        );
    }

    /**
     * The document length the user wrote in the sentence. Used only when no file is attached.
     */
    private function statedLength(StructuredAgentResponse $parsed): ?int
    {
        $stated = $parsed['total_pages'] ?? null;

        return is_int($stated) && $stated > 0 ? $stated : null;
    }
}
