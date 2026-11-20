<?php

namespace App\Filament\Resources\Shops\Tables;

use App\Enums\ShopStatus;
use App\Models\Shop;
use App\Services\Shops\ShopPublisher;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (ShopStatus $state) => $state->label())
                    ->color(fn (ShopStatus $state) => $state === ShopStatus::Published ? 'success' : 'warning'),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('last_surveyed_at')->label('Terakhir disurvei')->dateTime('d M Y')->placeholder('Belum disurvei')->sortable(),
                TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')
                    ->options(collect(ShopStatus::cases())->mapWithKeys(fn (ShopStatus $status) => [$status->value => $status->label()])),
                TernaryFilter::make('surveyed')->label('Sudah disurvei')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('last_surveyed_at'),
                        false: fn (Builder $query) => $query->whereNull('last_surveyed_at'),
                    ),
            ])
            ->recordActions([
                Action::make('publish')->label('Terbitkan')->icon('heroicon-o-eye')
                    ->visible(fn (Shop $record) => ! $record->isPublished())
                    ->requiresConfirmation()
                    ->modalDescription('Tempat ini akan tampil di website.')
                    ->action(fn (Shop $record) => self::publish(new Collection([$record]))),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')->label('Terbitkan terpilih')->icon('heroicon-o-eye')
                        ->requiresConfirmation()
                        ->modalDescription('Semua tempat yang dipilih akan tampil di website. Pastikan datanya sudah diperiksa.')
                        ->action(fn (Collection $records) => self::publish($records))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    /**
     * @param  Collection<int, Shop>  $shops
     */
    private static function publish(Collection $shops): void
    {
        $result = app(ShopPublisher::class)->publish($shops);

        if ($result['withoutHours'] > 0) {
            Notification::make()->warning()
                ->title("{$result['withoutHours']} tempat belum diterbitkan")
                ->body('Isi jam buka dulu di halaman ubah tempat, lalu terbitkan lagi.')
                ->send();
        }

        if ($result['published'] > 0) {
            Notification::make()->success()->title("{$result['published']} tempat diterbitkan")->send();
        }
    }
}
