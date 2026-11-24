<?php

namespace App\Filament\Resources\Reports;

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Models\Report;
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
 * Admins review what users reported about a shop. Fixing the data itself happens under "Tempat printing";
 * the report only records the decision and the admin's note.
 */
class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Laporan data';

    protected static ?string $modelLabel = 'laporan data';

    protected static ?string $pluralModelLabel = 'laporan data';

    public static function getNavigationBadge(): ?string
    {
        $waiting = Report::where('status', ReportStatus::Pending->value)->count();

        return $waiting > 0 ? (string) $waiting : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('shop_name')->label('Tempat')->disabled()->dehydrated(false),
            TextInput::make('reporter_email')->label('Pelapor')->disabled()->dehydrated(false),
            TextInput::make('category_label')->label('Jenis masalah')->disabled()->dehydrated(false),
            Select::make('status')->label('Status')->required()->options(self::statusOptions()),
            Textarea::make('message')->label('Pesan pelapor')->disabled()->dehydrated(false)->columnSpanFull(),
            Textarea::make('admin_note')->label('Catatan admin')->maxLength(500)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shop.name')->label('Tempat')->searchable(),
                TextColumn::make('category')->label('Jenis masalah')->badge()
                    ->formatStateUsing(fn (ReportCategory $state) => $state->label()),
                TextColumn::make('message')->label('Pesan')->limit(50),
                TextColumn::make('user.email')->label('Pelapor'),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (ReportStatus $state) => $state->label()),
                TextColumn::make('created_at')->label('Dikirim')->since()->sortable(),
            ])
            ->filters([SelectFilter::make('status')->label('Status')->options(self::statusOptions())])
            ->recordActions([EditAction::make()->label('Tinjau')])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
            'edit' => EditReport::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        return collect(ReportStatus::cases())
            ->mapWithKeys(fn (ReportStatus $status) => [$status->value => $status->label()])
            ->all();
    }
}
