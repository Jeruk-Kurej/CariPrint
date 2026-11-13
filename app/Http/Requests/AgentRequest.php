<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AgentRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        $maxPages = config('cariprint.uploads.max_pages');

        return [
            'request' => ['required', 'string', 'max:500'],
            'total_pages' => ['nullable', 'integer', 'min:1', "max:{$maxPages}"],
            'colored_pages' => ['nullable', 'array', "max:{$maxPages}"],
            'colored_pages.*' => ['integer', 'min:1', 'distinct'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'total_pages.max' => 'File PDF maksimal :max halaman.',
            'colored_pages.*' => 'Daftar halaman berwarna tidak valid. Lampirkan ulang file PDF-nya.',
            'colored_pages.max' => 'Daftar halaman berwarna tidak valid. Lampirkan ulang file PDF-nya.',
        ];
    }

    /**
     * The colour list only makes sense for a document of known length, and cannot point past its last page.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['total_pages', 'colored_pages']) || $this->coloredPages() === null) {
                    return;
                }

                $total = $this->documentPages();

                if ($total === null || max([0, ...$this->coloredPages()]) > $total) {
                    $validator->errors()->add('colored_pages', 'Daftar halaman berwarna tidak cocok dengan jumlah halaman file. Lampirkan ulang file PDF-nya.');
                }
            },
        ];
    }

    /**
     * Length of the PDF the user attached. The browser counts it and the file itself is never uploaded,
     * so this number only shapes the user's own estimate.
     */
    public function documentPages(): ?int
    {
        return $this->filled('total_pages') ? $this->integer('total_pages') : null;
    }

    /**
     * Pages the browser found colour on, or null when it did not check. An empty list is a real answer:
     * the file was checked and has no colour.
     *
     * @return list<int>|null
     */
    public function coloredPages(): ?array
    {
        $pages = $this->input('colored_pages');

        return is_array($pages) ? array_values(array_map('intval', $pages)) : null;
    }

    /**
     * User coordinates sent transiently to compute straight-line distance (never stored or logged).
     *
     * @return array{0: float, 1: float}|null
     */
    public function coordinates(): ?array
    {
        if (! $this->filled('latitude') || ! $this->filled('longitude')) {
            return null;
        }

        return [(float) $this->input('latitude'), (float) $this->input('longitude')];
    }
}
