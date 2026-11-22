<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Http\Requests\RecalculateRequest;
use App\Services\Ai\AiQuota;
use App\Services\Pricing\PrintRequestReader;
use App\Services\Pricing\PrintSpec;
use App\Services\Pricing\ShopEstimates;
use App\Services\Pricing\UnreadablePrintRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function __construct(private AiQuota $quota) {}

    public function index(Request $request): Response
    {
        return Inertia::render('agent/index', $this->formProps($request));
    }

    public function parse(AgentRequest $request, PrintRequestReader $reader, ShopEstimates $estimates): Response
    {
        if (! $this->quota->hasRemaining($request)) {
            throw ValidationException::withMessages(['request' => $this->quota->exhaustedMessage($request)]);
        }

        try {
            $spec = $reader->read($request->string('request')->toString(), $request->documentPages(), $request->coloredPages());
        } catch (UnreadablePrintRequest $exception) {
            throw ValidationException::withMessages(['request' => $exception->getMessage()]);
        }

        $this->quota->consume($request);

        return $this->result($request, $spec, $estimates, $request->coordinates());
    }

    /**
     * Prices a spec the user edited on the slip. No AI is called, so it does not use the daily quota.
     */
    public function recalculate(RecalculateRequest $request, ShopEstimates $estimates): Response
    {
        return $this->result($request, $request->spec(), $estimates, $request->coordinates());
    }

    /**
     * @param  array{0: float, 1: float}|null  $coordinates
     */
    private function result(Request $request, PrintSpec $spec, ShopEstimates $estimates, ?array $coordinates): Response
    {
        return Inertia::render('agent/index', [
            ...$this->formProps($request),
            'spec' => [
                'paperSize' => $spec->paperSize->value,
                'bwPages' => $spec->bwPages,
                'colorPages' => $spec->colorPages,
                'bwRuns' => $spec->pages?->blackWhiteRuns() ?? [],
                'colorRuns' => $spec->pages?->colorRuns() ?? [],
                'copies' => $spec->copies,
                'binding' => $spec->binding?->value,
                'assumed' => $spec->assumed,
            ],
            'estimates' => $estimates->forSpec($spec, $coordinates),
            'shopsWithoutPrices' => $estimates->withoutPrices(),
        ]);
    }

    /**
     * Limits the browser checks before it reads an attached PDF, and what is left of today's quota.
     *
     * @return array{fileLimits: array{maxMegabytes: int, maxPages: int}, quota: array{limit: int|null, remaining: int|null, isGuest: bool}}
     */
    private function formProps(Request $request): array
    {
        return [
            'quota' => $this->quota->status($request),
            'fileLimits' => [
                'maxMegabytes' => config('cariprint.uploads.max_megabytes'),
                'maxPages' => config('cariprint.uploads.max_pages'),
            ],
        ];
    }
}
