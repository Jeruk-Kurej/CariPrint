<?php

namespace App\Filament\Resources\ShopSuggestions\Pages;

use App\Filament\Resources\ShopSuggestions\ShopSuggestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShopSuggestion extends EditRecord
{
    protected static string $resource = ShopSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
