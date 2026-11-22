<?php

namespace App\Http\Requests\Concerns;

/**
 * The optional coordinates a request may carry for one distance calculation.
 */
trait ReadsCoordinates
{
    /**
     * @return array<string, list<string>>
     */
    protected function coordinateRules(): array
    {
        return [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * User coordinates sent transiently to compute straight-line distance (never stored or logged).
     *
     * @return array{0: float, 1: float}|null
     */
    public function coordinates(): ?array
    {
        if (! $this->filled('latitude') || ! $this->filled('longitude')) {
            return null;
        }

        return [(float) $this->input('latitude'), (float) $this->input('longitude')];
    }
}
