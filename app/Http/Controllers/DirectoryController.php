<?php

namespace App\Http\Controllers;

use App\Http\Requests\NearbyShopsRequest;
use App\Models\Shop;
use App\Services\Directory\ShopDirectory;
use App\Services\Directory\ShopPageMeta;
use Inertia\Inertia;
use Inertia\Response;

class DirectoryController extends Controller
{
    public function index(ShopDirectory $directory): Response
    {
        return Inertia::render('directory/index', [
            'shops' => $directory->list(),
        ]);
    }

    public function show(Shop $shop, ShopDirectory $directory, ShopPageMeta $meta): Response
    {
        abort_unless($shop->isPublished(), 404);

        $detail = $directory->detail($shop);

        return Inertia::render('directory/show', ['shop' => $detail])
            ->withViewData(['meta' => $meta->for($detail)]);
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
