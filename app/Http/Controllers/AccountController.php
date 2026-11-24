<?php

namespace App\Http\Controllers;

use App\Services\Reports\ReportHistory;
use App\Services\Suggestions\SuggestionHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __invoke(Request $request, SuggestionHistory $history, ReportHistory $reports): Response
    {
        $user = $request->user();

        return Inertia::render('account/show', [
            'suggestions' => $history->forEmail($user->email),
            'reports' => $reports->forUser($user),
            'isAdmin' => $user->isAdmin(),
        ]);
    }
}
