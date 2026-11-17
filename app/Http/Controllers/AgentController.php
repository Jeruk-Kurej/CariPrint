<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Services\Pricing\PrintRequestReader;
use App\Services\Pricing\ShopEstimates;
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

    public function parse(AgentRequest $request, PrintRequestReader $reader, ShopEstimates $estimates): Response
    {
        try {
            $spec = $reader->read($request->string('request')->toString(), $request->documentPages(), $request->coloredPages());
        } catch (UnreadablePrintRequest $exception) {
            throw ValidationException::withMessages(['request' => $exception->getMessage()]);
        }

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
            'estimates' => $estimates->forSpec($spec, $request->coordinates()),
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
