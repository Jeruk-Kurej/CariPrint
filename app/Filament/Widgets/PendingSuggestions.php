<?php

namespace App\Filament\Widgets;

use App\Enums\SuggestionStatus;
use App\Filament\Resources\ShopSuggestions\ShopSuggestionResource;
use App\Models\ShopSuggestion;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * The newest suggestions that still wait for a decision, so an admin can act right from the dashboard.
 */
class PendingSuggestions extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Usulan tempat menunggu';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ShopSuggestion::query()->where('status', SuggestionStatus::Pending->value)->latest())
            ->columns([
                TextColumn::make('name')->label('Nama tempat')->weight('bold'),
                TextColumn::make('address')->label('Alamat')->limit(50),
                TextColumn::make('contact_email')->label('Pengusul'),
                TextColumn::make('created_at')->label('Dikirim')->since(),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Tinjau')
                    ->url(fn (ShopSuggestion $record): string => ShopSuggestionResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated([5])
            ->emptyStateHeading('Tidak ada usulan yang menunggu')
            ->emptyStateDescription('Usulan baru dari pengunjung muncul di sini.');
    }
}
