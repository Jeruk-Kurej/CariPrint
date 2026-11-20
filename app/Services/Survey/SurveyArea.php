<?php

namespace App\Services\Survey;

use App\Services\Location\DistanceService;

/**
 * The part of the map an import accepts: a bounding box or a circle around a point.
 */
final readonly class SurveyArea
{
    /**
     * @param  array{min_latitude: float, max_latitude: float, min_longitude: float, max_longitude: float}|null  $bounds
     * @param  array{latitude: float, longitude: float, radiusKm: float}|null  $circle
     */
    private function __construct(
        public string $label,
        private ?array $bounds,
        private ?array $circle,
    ) {}

    /**
     * @param  array{min_latitude: float, max_latitude: float, min_longitude: float, max_longitude: float}  $bounds
     */
    public static function box(array $bounds, string $label): self
    {
        return new self($label, $bounds, null);
    }

    public static function circle(float $latitude, float $longitude, float $radiusKm): self
    {
        return new self("radius {$radiusKm} km", null, ['latitude' => $latitude, 'longitude' => $longitude, 'radiusKm' => $radiusKm]);
    }

    public function contains(float $latitude, float $longitude): bool
    {
        if ($this->bounds !== null) {
            return $latitude >= $this->bounds['min_latitude'] && $latitude <= $this->bounds['max_latitude']
                && $longitude >= $this->bounds['min_longitude'] && $longitude <= $this->bounds['max_longitude'];
        }

        return $this->circle !== null
            && (new DistanceService)->kilometers($this->circle['latitude'], $this->circle['longitude'], $latitude, $longitude) <= $this->circle['radiusKm'];
    }
}
