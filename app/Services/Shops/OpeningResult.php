<?php

namespace App\Services\Shops;

use App\Enums\OpenState;
use Carbon\CarbonImmutable;

readonly class OpeningResult
{
    public function __construct(
        public OpenState $state,
        public ?CarbonImmutable $closesAt = null,
        public ?CarbonImmutable $opensAt = null,
    ) {}
}
