<?php

namespace App\Services\Pricing;

readonly class PriceLine
{
    public function __construct(
        public string $label,
        public int $unitPrice,
        public int $quantity,
    ) {}

    public function subtotal(): int
    {
        return $this->unitPrice * $this->quantity;
    }
}
