<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopSuggestionRequest extends FormRequest
{
    /**
     * "website" is a hidden trap field: people never see it, so anything typed there comes from a bot.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'maps_url' => ['nullable', 'url:http,https', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama tempat',
            'address' => 'alamat',
            'maps_url' => 'tautan Google Maps',
            'note' => 'catatan',
        ];
    }
}
