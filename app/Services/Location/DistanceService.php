<?php

namespace App\Services\Location;

/**
 * Straight-line (Haversine) distance. Route-based distance comes later from the Routes API (task 1.5).
 */
class DistanceService
{
    private const EARTH_RADIUS_KM = 6371.0;

    public function kilometers(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): float
    {
        $dLat = deg2rad($toLatitude - $fromLatitude);
        $dLng = deg2rad($toLongitude - $fromLongitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($fromLatitude)) * cos(deg2rad($toLatitude)) * sin($dLng / 2) ** 2;

        return self::EARTH_RADIUS_KM * 2 * asin(min(1.0, sqrt($a)));
    }
}
