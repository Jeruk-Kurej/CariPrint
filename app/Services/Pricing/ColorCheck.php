<?php

namespace App\Services\Pricing;

use App\Enums\ColorNeed;

/**
 * Decides which pages of a PDF need colour. The browser reads the file on the user's device and sends only the
 * share of each page that is coloured, so the rules live here, in one place, and the thresholds in config.
 * The user always makes the final choice.
 */
class ColorCheck
{
    public function need(float $share): ColorNeed
    {
        return match (true) {
            $share <= (float) config('cariprint.color_check.bw_up_to') => ColorNeed::BlackWhite,
            $share >= (float) config('cariprint.color_check.color_from') => ColorNeed::Color,
            default => ColorNeed::Maybe,
        };
    }

    /**
     * @param  list<float>  $shares  Share of each page that is coloured, page 1 first.
     * @return list<ColorNeed>
     */
    public function classify(array $shares): array
    {
        return array_map($this->need(...), $shares);
    }

    /**
     * Pages (counted from 1) that clearly need colour. These are the suggested colour pages.
     *
     * @param  list<float>  $shares
     * @return list<int>
     */
    public function suggestedColorPages(array $shares): array
    {
        return $this->pagesWhere($shares, fn (ColorNeed $need) => $need === ColorNeed::Color);
    }

    /**
     * Every page with any real colour on it, clear or doubtful. Printing all of these in colour is the
     * usual way a coloured PDF is printed, so it is the baseline the savings are measured against.
     *
     * @param  list<float>  $shares
     * @return list<int>
     */
    public function coloredPages(array $shares): array
    {
        return $this->pagesWhere($shares, fn (ColorNeed $need) => $need !== ColorNeed::BlackWhite);
    }

    /**
     * @param  list<float>  $shares
     * @param  callable(ColorNeed): bool  $keep
     * @return list<int>
     */
    private function pagesWhere(array $shares, callable $keep): array
    {
        $pages = [];

        foreach ($this->classify($shares) as $index => $need) {
            if ($keep($need)) {
                $pages[] = $index + 1;
            }
        }

        return $pages;
    }
}
