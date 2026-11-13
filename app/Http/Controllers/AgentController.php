<?php

namespace App\Http\Controllers;

use App\Ai\Agents\PrintRequestAgent;
use App\Enums\PaperSize;
use App\Enums\ShopService;
use App\Http\Requests\AgentRequest;
use App\Models\Shop;
use App\Services\Pricing\PriceCalculator;
use App\Services\Pricing\PrintSpec;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('agent/index');
    }

    public function parse(AgentRequest $request, PriceCalculator $calculator): Response
    {
        try {
            $parsed = (new PrintRequestAgent)->prompt($request->string('request')->toString());
        } catch (Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'request' => 'Asisten belum bisa dipakai saat ini. Coba lagi sebentar lagi.',
            ]);
        }

        $spec = new PrintSpec(
            paperSize: PaperSize::from($parsed['paper_size']),
            bwPages: (int) $parsed['bw_pages'],
            colorPages: (int) $parsed['color_pages'],
            copies: (int) $parsed['copies'],
            binding: $parsed['binding'] ? ShopService::from($parsed['binding']) : null,
        );

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
            'spec' => [
                'paperSize' => $spec->paperSize->value,
                'bwPages' => $spec->bwPages,
                'colorPages' => $spec->colorPages,
                'copies' => $spec->copies,
                'binding' => $spec->binding?->value,
            ],
            'estimates' => $estimates,
            'hasDemoPrices' => $shops->contains(fn (Shop $shop) => $shop->hasDemoPrices()),
        ]);
    }
}
