<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ContactCategory;
use App\Enums\ContactStatus;
use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
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
 * Messages from the "Hubungi kami" form. The admin answers by email, then marks the message here.
 */
class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Pesan masuk';

    protected static ?string $modelLabel = 'pesan masuk';

    protected static ?string $pluralModelLabel = 'pesan masuk';

    public static function getNavigationBadge(): ?string
    {
        $new = ContactMessage::where('status', ContactStatus::New->value)->count();

        return $new > 0 ? (string) $new : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('category_label')->label('Jenis pesan')->disabled()->dehydrated(false),
            TextInput::make('email')->label('Email pengirim')->disabled()->dehydrated(false),
            Select::make('status')->label('Status')->required()->options(self::statusOptions()),
            Textarea::make('message')->label('Pesan')->disabled()->dehydrated(false)->columnSpanFull(),
            Textarea::make('admin_note')->label('Catatan admin')->maxLength(500)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')->label('Jenis pesan')->badge()
                    ->formatStateUsing(fn (ContactCategory $state) => $state->label()),
                TextColumn::make('email')->label('Pengirim')->searchable(),
                TextColumn::make('message')->label('Pesan')->limit(50),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (ContactStatus $state) => $state->label()),
                TextColumn::make('created_at')->label('Dikirim')->since()->sortable(),
            ])
            ->filters([SelectFilter::make('status')->label('Status')->options(self::statusOptions())])
            ->recordActions([EditAction::make()->label('Buka')])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        return collect(ContactStatus::cases())
            ->mapWithKeys(fn (ContactStatus $status) => [$status->value => $status->label()])
            ->all();
    }
}
