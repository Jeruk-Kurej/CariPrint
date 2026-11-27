<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopSearchRequest;
use App\Services\Ai\AiQuota;
use App\Services\Directory\SearchAssistantUnavailable;
use App\Services\Directory\SearchRequestReader;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ShopSearchController extends Controller
{
    /**
     * Answers with JSON, not an Inertia page: the directory keeps its list and distances and only swaps its filters.
     * Only the sentence is sent here, never the visitor's location.
     */
    public function __invoke(ShopSearchRequest $request, SearchRequestReader $reader, AiQuota $quota): JsonResponse
    {
        if (! $quota->hasRemaining($request)) {
            throw ValidationException::withMessages(['sentence' => $quota->exhaustedMessage($request)]);
        }

        try {
            $filters = $reader->read($request->string('sentence')->toString());
        } catch (SearchAssistantUnavailable $exception) {
            throw ValidationException::withMessages(['sentence' => $exception->getMessage()]);
        }

        $quota->consume($request);

        return response()->json([
            'filters' => $filters->isEmpty() ? null : $filters->toChoices(),
            'area' => $filters->area,
            'quota' => $quota->status($request),
        ]);
    }
}
