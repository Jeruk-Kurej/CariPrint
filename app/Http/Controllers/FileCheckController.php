<?php

namespace App\Http\Controllers;

use App\Enums\ColorNeed;
use App\Http\Requests\FileCheckRequest;
use App\Services\Pricing\ColorCheck;
use App\Services\Pricing\ColorSavings;
use Illuminate\Http\JsonResponse;

class FileCheckController extends Controller
{
    /**
     * Judges each page of a PDF the browser already read and prices what the user's colour choice saves.
     */
    public function __invoke(FileCheckRequest $request, ColorCheck $check, ColorSavings $savings): JsonResponse
    {
        $shares = $request->shares();
        $suggested = $check->suggestedColorPages($shares);

        return response()->json([
            'needs' => array_map(fn (ColorNeed $need) => $need->value, $check->classify($shares)),
            'suggested' => $suggested,
            'savings' => $savings->forPages(count($shares), $request->chosenPages() ?? $suggested, $check->coloredPages($shares)),
        ]);
    }
}
