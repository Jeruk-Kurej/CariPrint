<?php

namespace App\Filament\Resources\Shops\Schemas;

use App\Enums\ShopService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ShopForm
{
    private const DAYS = [
        'mon' => 'Senin', 'tue' => 'Selasa', 'wed' => 'Rabu', 'thu' => 'Kamis',
        'fri' => 'Jumat', 'sat' => 'Sabtu', 'sun' => 'Minggu',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data tempat')->schema([
                TextInput::make('name')->label('Nama')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('address')->label('Alamat')->required(),
                TextInput::make('whatsapp')->label('WhatsApp (format 62...)')->tel(),
                TextInput::make('place_id')->label('Google place_id')
                    ->helperText('Hanya place_id yang boleh dari Google. Koordinat diisi dari survei.'),
                DateTimePicker::make('last_surveyed_at')->label('Terakhir disurvei'),
            ])->columns(2),

            Section::make('Lokasi')->schema([
                TextInput::make('latitude')->required()->numeric()->minValue(-90)->maxValue(90),
                TextInput::make('longitude')->required()->numeric()->minValue(-180)->maxValue(180),
            ])->columns(2),

            Section::make('Jam buka')->schema(
                collect(self::DAYS)->map(fn (string $label, string $key) => Repeater::make("opening_hours.$key")
                    ->label($label)
                    ->schema([
                        TextInput::make('open')->label('Buka')->placeholder('08:00')->required(),
                        TextInput::make('close')->label('Tutup')->placeholder('21:00')->required(),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel('Tambah jam')
                )->values()->all()
            ),

            Section::make('Layanan dan halaman warna')->schema([
                CheckboxList::make('services')
                    ->label('Layanan')
                    ->options(collect(ShopService::cases())->mapWithKeys(fn (ShopService $s) => [$s->value => $s->name]))
                    ->columns(2),
                Textarea::make('color_page_rule')
                    ->label('Aturan halaman warna (dari penjelasan penjaga)')
                    ->columnSpanFull(),
            ]),
        ]);
    }
}
