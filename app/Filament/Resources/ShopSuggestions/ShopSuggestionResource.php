<?php

namespace App\Filament\Resources\ShopSuggestions;

use App\Enums\SuggestionStatus;
use App\Filament\Resources\ShopSuggestions\Pages\EditShopSuggestion;
use App\Filament\Resources\ShopSuggestions\Pages\ListShopSuggestions;
use App\Models\ShopSuggestion;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Admins review what visitors suggested. Accepting a suggestion does not create a shop:
 * the admin adds the shop under "Tempat printing" after checking the details.
 */
class ShopSuggestionResource extends Resource
{
    protected static ?string $model = ShopSuggestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $navigationLabel = 'Usulan tempat';

    protected static ?string $modelLabel = 'usulan tempat';

    protected static ?string $pluralModelLabel = 'usulan tempat';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama tempat')->disabled(),
            TextInput::make('address')->label('Alamat')->disabled(),
            TextInput::make('maps_url')->label('Tautan Google Maps')->disabled(),
            TextInput::make('contact_email')->label('Email pengusul')->disabled(),
            Textarea::make('note')->label('Catatan pengusul')->disabled()->columnSpanFull(),
            Select::make('status')->label('Status')->required()->options(self::statusOptions()),
            Textarea::make('admin_note')->label('Catatan admin')->maxLength(500)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama tempat')->searchable(),
                TextColumn::make('address')->label('Alamat')->limit(40),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (SuggestionStatus $state) => $state->label()),
                TextColumn::make('created_at')->label('Dikirim')->since()->sortable(),
            ])
            ->filters([SelectFilter::make('status')->label('Status')->options(self::statusOptions())])
            ->recordActions([EditAction::make()->label('Tinjau')])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShopSuggestions::route('/'),
            'edit' => EditShopSuggestion::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        return collect(SuggestionStatus::cases())
            ->mapWithKeys(fn (SuggestionStatus $status) => [$status->value => $status->label()])
            ->all();
    }
}
