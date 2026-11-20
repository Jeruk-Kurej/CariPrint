<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShopSuggestionRequest;
use App\Services\Suggestions\SuggestionSubmitter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopSuggestionController extends Controller
{
    /**
     * Anyone can read about suggesting a place; only a signed-in user sees the form. A guest who signs in from here
     * comes back to this page.
     */
    public function create(Request $request): Response
    {
        if ($request->user() === null) {
            $request->session()->put('url.intended', $request->url());
        }

        return Inertia::render('suggestions/create', [
            'submitted' => (bool) $request->session()->get('suggestion_submitted', false),
            'email' => $request->user()?->email,
        ]);
    }

    public function store(StoreShopSuggestionRequest $request, SuggestionSubmitter $submitter): RedirectResponse
    {
        $submitter->submit([...$request->safe()->except('website'), 'contact_email' => $request->user()?->email]);

        return to_route('suggestions.create')->with('suggestion_submitted', true);
    }
}
