<?php

namespace App\Services\Directory;

use App\Enums\ColorMode;
use App\Enums\PaperSize;

/**
 * What a sentence asked the directory to show, limited to the choices the page offers.
 */
readonly class SearchFilters
{
    /**
     * @param  list<string>  $services
     */
    public function __construct(
        public string $area = '',
        public string $shopName = '',
        public ?PaperSize $paper = null,
        public ?ColorMode $color = null,
        public array $services = [],
        public ?string $sort = null,
        public bool $openOnly = false,
        public ?int $maxKm = null,
    ) {}

    public function isEmpty(): bool
    {
        return $this->toChoices() === (new self)->toChoices();
    }

    /**
     * The same shape as the directory page's own filters.
     *
     * @return array{query: string, openOnly: bool, sort: string|null, paper: string, color: string, services: list<string>, maxKm: int|null}
     */
    public function toChoices(): array
    {
        return [
            'query' => trim("{$this->shopName} {$this->area}"),
            'openOnly' => $this->openOnly,
            'sort' => $this->sort,
            'paper' => $this->paper->value ?? 'any',
            'color' => $this->color->value ?? 'any',
            'services' => $this->services,
            'maxKm' => $this->maxKm,
        ];
    }
}
