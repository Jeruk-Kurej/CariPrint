<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopSearchRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'sentence' => ['required', 'string', 'max:200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sentence.required' => 'Tulis dulu yang kamu cari di kotak pencarian.',
            'sentence.max' => 'Kalimatnya maksimal :max karakter.',
        ];
    }
}
