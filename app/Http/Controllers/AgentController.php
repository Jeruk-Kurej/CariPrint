<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Models\Shop;
use App\Services\Pricing\PriceCalculator;
use App\Services\Pricing\PrintRequestReader;
use App\Services\Pricing\UnreadablePrintRequest;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('agent/index', $this->formProps());
    }

    public function parse(AgentRequest $request, PrintRequestReader $reader, PriceCalculator $calculator): Response
    {
        try {
            $spec = $reader->read($request->string('request')->toString(), $request->documentPages());
        } catch (UnreadablePrintRequest $exception) {
            throw ValidationException::withMessages(['request' => $exception->getMessage()]);
        }

        $shops = Shop::query()->with('priceLists')->orderBy('name')->get();

        $estimates = $shops
            ->map(function (Shop $shop) use ($calculator, $spec) {
                $breakdown = $calculator->forShop($shop, $spec);

                return [
                    'name' => $shop->name,
                    'slug' => $shop->slug,
                    'total' => $breakdown->total(),
                    'missing' => $breakdown->missing,
                ];
            })
            ->sortBy(fn (array $row) => $row['total'] ?? PHP_INT_MAX)
            ->values();

        return Inertia::render('agent/index', [
            ...$this->formProps(),
            'spec' => [
                'paperSize' => $spec->paperSize->value,
                'bwPages' => $spec->bwPages,
                'colorPages' => $spec->colorPages,
                'bwRuns' => $spec->pages?->blackWhiteRuns() ?? [],
                'colorRuns' => $spec->pages?->colorRuns() ?? [],
                'copies' => $spec->copies,
                'binding' => $spec->binding?->value,
            ],
            'estimates' => $estimates,
        ]);
    }

    /**
     * Limits the browser checks before it reads an attached PDF.
     *
     * @return array{fileLimits: array{maxMegabytes: int, maxPages: int}}
     */
    private function formProps(): array
    {
        return [
            'fileLimits' => [
                'maxMegabytes' => config('cariprint.uploads.max_megabytes'),
                'maxPages' => config('cariprint.uploads.max_pages'),
            ],
        ];
    }
}
