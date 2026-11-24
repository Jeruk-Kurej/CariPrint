<?php

namespace App\Http\Controllers;

use App\Enums\ContactCategory;
use App\Http\Requests\StoreContactMessageRequest;
use App\Services\Contact\ContactSubmitter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('info/contact', [
            'categories' => ContactCategory::options(),
            'email' => $request->user()?->email,
            'sent' => (bool) $request->session()->get('contact_sent', false),
        ]);
    }

    public function store(StoreContactMessageRequest $request, ContactSubmitter $submitter): RedirectResponse
    {
        $submitter->submit($request->safe()->only(['category', 'message']), $request->replyEmail(), $request->user());

        return to_route('contact.create')->with('contact_sent', true);
    }
}
