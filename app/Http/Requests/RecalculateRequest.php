<?php

namespace App\Http\Requests;

use App\Enums\PaperSize;
use App\Enums\ShopService;
use App\Http\Requests\Concerns\ReadsCoordinates;
use App\Services\Pricing\PageSelection;
use App\Services\Pricing\PrintSpec;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A print spec the user edited on the slip. It is priced again without the AI, so it costs no quota.
 */
class RecalculateRequest extends FormRequest
{
    use ReadsCoordinates;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxPages = config('cariprint.uploads.max_pages');

        return [
            'paper_size' => ['required', Rule::enum(PaperSize::class)],
            'copies' => ['required', 'integer', 'min:1', 'max:100'],
            'binding' => ['nullable', Rule::in([
                ShopService::SpiralBinding->value,
                ShopService::TapeBinding->value,
                ShopService::HardcoverBinding->value,
            ])],
            'bw_pages' => ['required', 'integer', 'min:0', 'max:10000'],
            'color_pages' => ['required', 'integer', 'min:0', 'max:10000'],
            'bw_runs' => ['nullable', 'array', 'max:50'],
            'color_runs' => ['nullable', 'array', 'max:50'],
            'bw_runs.*.from' => ['required', 'integer', 'min:1', "max:{$maxPages}"],
            'bw_runs.*.to' => ['required', 'integer', 'gte:bw_runs.*.from', "max:{$maxPages}"],
            'color_runs.*.from' => ['required', 'integer', 'min:1', "max:{$maxPages}"],
            'color_runs.*.to' => ['required', 'integer', 'gte:color_runs.*.from', "max:{$maxPages}"],
            'assumed' => ['nullable', 'array'],
            'assumed.*' => [Rule::in(['paperSize', 'copies'])],
            ...$this->coordinateRules(),
        ];
    }

    public function spec(): PrintSpec
    {
        $bwRuns = $this->input('bw_runs', []);
        $colorRuns = $this->input('color_runs', []);

        return new PrintSpec(
            paperSize: PaperSize::from($this->string('paper_size')->toString()),
            bwPages: $this->integer('bw_pages'),
            colorPages: $this->integer('color_pages'),
            copies: $this->integer('copies'),
            binding: ShopService::tryFrom($this->string('binding')->toString()),
            pages: $bwRuns === [] && $colorRuns === [] ? null : PageSelection::fromRuns($bwRuns, $colorRuns),
            assumed: array_values(array_unique($this->input('assumed', []))),
        );
    }
}
