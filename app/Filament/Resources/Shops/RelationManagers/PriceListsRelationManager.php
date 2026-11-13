<?php

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceListsRelationManager extends RelationManager
{
    protected static string $relationship = 'priceLists';

    protected static ?string $title = 'Harga';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('service')->label('Layanan')->required()
                ->options(collect(ShopService::cases())->mapWithKeys(fn (ShopService $s) => [$s->value => $s->name])),
            Select::make('paper_size')->label('Ukuran kertas')
                ->options(collect(PaperSize::cases())->mapWithKeys(fn (PaperSize $p) => [$p->value => strtoupper($p->value)])),
            Select::make('color_mode')->label('Warna')
                ->options([ColorMode::BlackWhite->value => 'Hitam-putih', ColorMode::Color->value => 'Warna']),
            TextInput::make('price')->label('Harga (Rupiah)')->required()->integer()->minValue(0)->prefix('Rp'),
            Select::make('unit')->label('Satuan')->required()
                ->options([PriceUnit::Page->value => 'per halaman', PriceUnit::Item->value => 'per buku/lembar']),
            Select::make('source')->label('Sumber harga')->required()
                ->options([
                    PriceSource::Verified->value => 'Terverifikasi (survei)',
                    PriceSource::Estimated->value => 'Perkiraan',
                ])
                ->default(PriceSource::Estimated->value),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('service')
            ->columns([
                TextColumn::make('service')->label('Layanan')->formatStateUsing(fn ($state) => $state->name),
                TextColumn::make('paper_size')->label('Kertas')->placeholder('-'),
                TextColumn::make('color_mode')->label('Warna')->placeholder('-'),
                TextColumn::make('price')->label('Harga')->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.')),
                TextColumn::make('source')->label('Sumber')->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
