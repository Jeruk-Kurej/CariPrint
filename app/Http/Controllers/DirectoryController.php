<?php

namespace App\Http\Controllers;

use App\Enums\ReportCategory;
use App\Http\Requests\NearbyShopsRequest;
use App\Models\Shop;
use App\Services\Ai\AiQuota;
use App\Services\Directory\ShopDirectory;
use App\Services\Directory\ShopPageMeta;
use App\Services\Reports\ReportHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DirectoryController extends Controller
{
    public function index(Request $request, ShopDirectory $directory, AiQuota $quota): Response
    {
        return Inertia::render('directory/index', [
            'shops' => $directory->list(),
            'quota' => $quota->status($request),
        ]);
    }

    /**
     * A guest who signs in from the report button comes back to this page.
     */
    public function show(Request $request, Shop $shop, ShopDirectory $directory, ShopPageMeta $meta, ReportHistory $reports): Response
    {
        abort_unless($shop->isPublished(), 404);

        $user = $request->user();

        if ($user === null) {
            $request->session()->put('url.intended', $request->url());
        }

        $detail = $directory->detail($shop);

        return Inertia::render('directory/show', [
            'shop' => $detail,
            'reporting' => [
                'categories' => ReportCategory::options(),
                'hasPending' => $user !== null && $reports->hasPendingFor($user, $shop),
            ],
        ])->withViewData(['meta' => $meta->for($detail)]);
    }

    /**
     * Coordinates arrive in the request body, never in the URL, and are not stored.
     */
    public function nearby(NearbyShopsRequest $request, ShopDirectory $directory): Response
    {
        return Inertia::render('directory/index', [
            'shops' => $directory->list(
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
            ),
        ]);
    }
}
