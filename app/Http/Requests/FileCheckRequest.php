<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * The browser reads the PDF itself and sends only how much of each page is coloured, plus the pages the user
 * wants in colour. No file and no page content reaches the server.
 */
class FileCheckRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        $maxPages = config('cariprint.uploads.max_pages');

        return [
            'shares' => ['required', 'array', 'min:1', "max:{$maxPages}"],
            'shares.*' => ['required', 'numeric', 'between:0,1'],
            'chosen' => ['nullable', 'array', "max:{$maxPages}"],
            'chosen.*' => ['integer', 'min:1', 'distinct'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shares.*' => 'Data warna file tidak valid. Lampirkan ulang file PDF-nya.',
            'shares.max' => 'File PDF maksimal :max halaman.',
            'chosen.*' => 'Pilihan halaman berwarna tidak valid. Lampirkan ulang file PDF-nya.',
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['shares', 'shares.*', 'chosen', 'chosen.*']) || $this->chosenPages() === null) {
                    return;
                }

                if (max([0, ...$this->chosenPages()]) > count($this->shares())) {
                    $validator->errors()->add('chosen', 'Pilihan halaman berwarna tidak cocok dengan jumlah halaman file. Lampirkan ulang file PDF-nya.');
                }
            },
        ];
    }

    /**
     * @return list<float>
     */
    public function shares(): array
    {
        return array_values(array_map('floatval', $this->input('shares', [])));
    }

    /**
     * The pages the user picked for colour, or null when they have not picked yet and the suggestion applies.
     *
     * @return list<int>|null
     */
    public function chosenPages(): ?array
    {
        $pages = $this->input('chosen');

        return is_array($pages) ? array_values(array_map('intval', $pages)) : null;
    }
}
