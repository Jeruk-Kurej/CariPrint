<?php

namespace App\Services\Pricing;

/**
 * The pages that will be printed, as page numbers per colour mode.
 */
readonly class PageSelection
{
    /**
     * @param  list<int>  $blackWhite  Page numbers in ascending order.
     * @param  list<int>  $color  Page numbers in ascending order.
     */
    public function __construct(
        public array $blackWhite,
        public array $color,
    ) {}

    /**
     * @return list<array{from: int, to: int}>
     */
    public function blackWhiteRuns(): array
    {
        return $this->runs($this->blackWhite);
    }

    /**
     * @return list<array{from: int, to: int}>
     */
    public function colorRuns(): array
    {
        return $this->runs($this->color);
    }

    /**
     * Joins neighbouring page numbers, so 1, 2, 3, 7 reads as 1 to 3 and 7.
     *
     * @param  list<int>  $pages
     * @return list<array{from: int, to: int}>
     */
    private function runs(array $pages): array
    {
        $runs = [];

        foreach ($pages as $page) {
            $last = array_key_last($runs);

            if ($last !== null && $runs[$last]['to'] === $page - 1) {
                $runs[$last]['to'] = $page;

                continue;
            }

            $runs[] = ['from' => $page, 'to' => $page];
        }

        return $runs;
    }
}
