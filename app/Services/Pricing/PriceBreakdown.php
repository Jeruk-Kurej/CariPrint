<?php

namespace App\Services\Pricing;

readonly class PriceBreakdown
{
    /**
     * @param  list<PriceLine>  $lines
     * @param  list<string>  $missing  Services the shop does not have a price for.
     */
    public function __construct(
        public array $lines,
        public array $missing,
    ) {}

    /** A shop without a needed price is "tidak tersedia", never a total of zero. */
    public function isAvailable(): bool
    {
        return $this->missing === [];
    }

    public function total(): ?int
    {
        return $this->isAvailable()
            ? array_sum(array_map(fn (PriceLine $line) => $line->subtotal(), $this->lines))
            : null;
    }
}
