<?php

namespace App\Filament\Widgets;

use App\Enums\PriceSource;
use App\Enums\ReportStatus;
use App\Enums\ShopStatus;
use App\Enums\SuggestionStatus;
use App\Filament\Resources\Reports\ReportResource;
use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\ShopSuggestions\ShopSuggestionResource;
use App\Models\PriceList;
use App\Models\Report;
use App\Models\Shop;
use App\Models\ShopSuggestion;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Numbers an admin checks first: how many shops are listed, how many drafts wait for review, how many prices still wait for the owner's
 * confirmation, how many suggestions wait for a decision, and how many reports wait for a review.
 */
class ShopOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan';

    protected function getStats(): array
    {
        $drafts = Shop::where('status', ShopStatus::Draft->value)->count();
        $pending = ShopSuggestion::where('status', SuggestionStatus::Pending->value)->count();
        $reports = Report::where('status', ReportStatus::Pending->value)->count();
        $unconfirmed = PriceList::where('source', '!=', PriceSource::Verified->value)->count();

        return [
            Stat::make('Tempat printing', Shop::published()->count())
                ->description('Tampil di website')
                ->url(ShopResource::getUrl('index')),
            Stat::make('Draf menunggu diperiksa', $drafts)
                ->description($drafts > 0 ? 'Belum tampil di website' : 'Tidak ada draf')
                ->color($drafts > 0 ? 'warning' : 'success')
                ->url(ShopResource::getUrl('index', ['filters' => ['status' => ['value' => ShopStatus::Draft->value]]])),
            Stat::make('Harga belum dikonfirmasi', $unconfirmed)
                ->description('Masih berstatus Perkiraan')
                ->color($unconfirmed > 0 ? 'warning' : 'success'),
            Stat::make('Usulan menunggu', $pending)
                ->description($pending > 0 ? 'Perlu diputuskan' : 'Semua sudah diputuskan')
                ->color($pending > 0 ? 'warning' : 'success')
                ->url(ShopSuggestionResource::getUrl('index')),
            Stat::make('Laporan menunggu', $reports)
                ->description($reports > 0 ? 'Perlu diperiksa' : 'Semua sudah diputuskan')
                ->color($reports > 0 ? 'warning' : 'success')
                ->url(ReportResource::getUrl('index')),
        ];
    }
}
