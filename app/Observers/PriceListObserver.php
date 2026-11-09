<?php

namespace App\Observers;

use App\Models\PriceList;

/**
 * Whenever a price changes, the shop's "diubah" date moves too, so visitors can see how fresh the data is.
 */
class PriceListObserver
{
    public function saved(PriceList $priceList): void
    {
        $priceList->shop?->touch();
    }

    public function deleted(PriceList $priceList): void
    {
        $priceList->shop?->touch();
    }
}
