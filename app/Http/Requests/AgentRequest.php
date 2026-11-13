<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'request' => ['required', 'string', 'max:500'],
            'total_pages' => ['nullable', 'integer', 'min:1', 'max:'.config('cariprint.uploads.max_pages')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'total_pages.max' => 'File PDF maksimal :max halaman.',
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
}
