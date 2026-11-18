<?php

namespace App\Filament\Widgets;

use App\Enums\PriceSource;
use App\Enums\SuggestionStatus;
use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\ShopSuggestions\ShopSuggestionResource;
use App\Models\PriceList;
use App\Models\Shop;
use App\Models\ShopSuggestion;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Three numbers an admin checks first: how many shops are listed, how many prices still wait for the owner's
 * confirmation, and how many suggestions wait for a decision.
 */
class ShopOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan';

    protected function getStats(): array
    {
        $pending = ShopSuggestion::where('status', SuggestionStatus::Pending->value)->count();
        $unconfirmed = PriceList::where('source', '!=', PriceSource::Verified->value)->count();

        return [
            Stat::make('Tempat printing', Shop::count())
                ->description('Tampil di website')
                ->url(ShopResource::getUrl('index')),
            Stat::make('Harga belum dikonfirmasi', $unconfirmed)
                ->description('Masih berstatus Perkiraan')
                ->color($unconfirmed > 0 ? 'warning' : 'success'),
            Stat::make('Usulan menunggu', $pending)
                ->description($pending > 0 ? 'Perlu diputuskan' : 'Semua sudah diputuskan')
                ->color($pending > 0 ? 'warning' : 'success')
                ->url(ShopSuggestionResource::getUrl('index')),
        ];
    }
}
