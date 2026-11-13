<?php

namespace App\Services\Pricing;

use App\Enums\ColorMode;

/**
 * Works out which pages a request covers. The AI only names the ranges, and all counting happens here (core rule 1).
 */
class PageSelector
{
    /**
     * A later range wins on the pages it shares with an earlier one, so "everything black and white,
     * then pages 1 to 5 in colour" and "everything, then leave out the last page" both work.
     *
     * @param  list<PageRange>  $ranges
     * @param  int|null  $totalPages  Length of the document, or null when nobody knows it.
     * @param  list<int>|null  $coloredPages  Pages the browser found colour on, or null when no file was checked.
     *
     * @throws UnreadablePrintRequest
     */
    public function select(array $ranges, ?int $totalPages, ?array $coloredPages = null): PageSelection
    {
        $maxPages = config('cariprint.uploads.max_pages');

        if ($totalPages !== null && $totalPages > $maxPages) {
            throw UnreadablePrintRequest::overLimit($maxPages);
        }

        $modes = [];
        $colored = array_flip($coloredPages ?? []);

        foreach ($ranges as $range) {
            if ($range->auto && $coloredPages === null) {
                throw UnreadablePrintRequest::colourUnknown();
            }

            $from = $this->pageNumber($range->from, $totalPages, $maxPages);
            $to = $this->pageNumber($range->to, $totalPages, $maxPages);

            foreach (range(min($from, $to), max($from, $to)) as $page) {
                $modes[$page] = $range->auto
                    ? (isset($colored[$page]) ? ColorMode::Color : ColorMode::BlackWhite)
                    : $range->colorMode;
            }
        }

        ksort($modes);

        return new PageSelection(
            blackWhite: array_keys($modes, ColorMode::BlackWhite, true),
            color: array_keys($modes, ColorMode::Color, true),
        );
    }

    /**
     * @throws UnreadablePrintRequest
     */
    private function pageNumber(int $position, ?int $totalPages, int $maxPages): int
    {
        if ($position < 0 && $totalPages === null) {
            throw UnreadablePrintRequest::lengthUnknown();
        }

        $page = $position < 0 ? $totalPages + $position + 1 : $position;

        if ($page >= 1 && $page <= ($totalPages ?? $maxPages)) {
            return $page;
        }

        throw $totalPages === null
            ? UnreadablePrintRequest::overLimit($maxPages)
            : UnreadablePrintRequest::outsideDocument($totalPages);
    }
}
