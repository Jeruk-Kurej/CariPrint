<?php

namespace App\Http\Requests;

use App\Enums\ReportCategory;
use App\Models\Shop;
use App\Services\Reports\ReportHistory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    /**
     * A user may have one report per shop waiting for an admin, so a single complaint cannot flood the review list.
     *
     * @return array<string, array<int, ValidationRule|Closure|string>>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(ReportCategory::class), $this->noPendingReportForThisShop(...)],
            'message' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Pilih jenis masalahnya dulu.',
            'category.enum' => 'Jenis masalah yang kamu pilih tidak dikenal.',
            'message.required' => 'Tulis pesannya dulu.',
            'message.min' => 'Ceritakan sedikit lebih jelas, minimal :min karakter.',
            'message.max' => 'Pesannya terlalu panjang, maksimal :max karakter.',
        ];
    }

    private function noPendingReportForThisShop(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Shop $shop */
        $shop = $this->route('shop');

        if (app(ReportHistory::class)->hasPendingFor($this->user(), $shop)) {
            $fail('Laporanmu untuk tempat ini masih ditinjau admin. Tunggu hasilnya dulu.');
        }
    }
}
