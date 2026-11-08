<?php

namespace App\Filament\Resources\Shops\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('last_surveyed_at')->label('Terakhir disurvei')->dateTime('d M Y')->placeholder('Belum disurvei')->sortable(),
                TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('surveyed')->label('Sudah disurvei')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('last_surveyed_at'),
                        false: fn (Builder $query) => $query->whereNull('last_surveyed_at'),
                    ),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('updated_at', 'desc');
    }
}
