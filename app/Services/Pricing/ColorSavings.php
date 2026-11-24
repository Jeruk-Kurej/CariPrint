<?php

namespace App\Services\Pricing;

use App\Enums\PaperSize;
use App\Models\Shop;
use Illuminate\Support\Collection;

/**
 * What choosing fewer colour pages saves. The baseline prints every page that has colour on it in colour.
 * The comparison uses A4 and one copy because the file check runs before the user describes the print job,
 * and it names the cheapest shop that has a price for both black and white and colour.
 */
class ColorSavings
{
    public function __construct(private readonly PriceCalculator $calculator) {}

    /**
     * @param  list<int>  $chosenColorPages  Pages the user wants in colour.
     * @param  list<int>  $coloredPages  Every page with colour on it.
     * @return array{shop: string, slug: string, chosenTotal: int, allColorTotal: int, allBlackWhiteTotal: int, saving: int}|null
     */
    public function forPages(int $totalPages, array $chosenColorPages, array $coloredPages): ?array
    {
        $shops = Shop::published()->has('priceLists')->with('priceLists')->get();

        return $this->fromShops($shops, $totalPages, $chosenColorPages, $coloredPages);
    }

    /**
     * @param  Collection<int, Shop>  $shops
     * @param  list<int>  $chosenColorPages
     * @param  list<int>  $coloredPages
     * @return array{shop: string, slug: string, chosenTotal: int, allColorTotal: int, allBlackWhiteTotal: int, saving: int}|null
     */
    public function fromShops(Collection $shops, int $totalPages, array $chosenColorPages, array $coloredPages): ?array
    {
        if ($coloredPages === []) {
            return null;
        }

        $best = null;

        foreach ($shops as $shop) {
            $chosen = $this->total($shop, $totalPages, count($chosenColorPages));
            $allColor = $this->total($shop, $totalPages, count($coloredPages));
            $allBlackWhite = $this->total($shop, $totalPages, 0);

            if ($chosen === null || $allColor === null || $allBlackWhite === null) {
                continue;
            }

            if ($best === null || $chosen < $best['chosenTotal']) {
                $best = [
                    'shop' => $shop->name,
                    'slug' => $shop->slug,
                    'chosenTotal' => $chosen,
                    'allColorTotal' => $allColor,
                    'allBlackWhiteTotal' => $allBlackWhite,
                    'saving' => $allColor - $chosen,
                ];
            }
        }

        return $best;
    }

    private function total(Shop $shop, int $totalPages, int $colorPages): ?int
    {
        return $this->calculator
            ->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: $totalPages - $colorPages, colorPages: $colorPages))
            ->total();
    }
}
