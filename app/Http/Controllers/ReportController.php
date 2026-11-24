<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Shop;
use App\Services\Reports\ReportSubmitter;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function store(Shop $shop, StoreReportRequest $request, ReportSubmitter $submitter): RedirectResponse
    {
        abort_unless($shop->isPublished(), 404);

        $submitter->submit($request->user(), $shop, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Terima kasih, laporanmu sudah masuk. Admin akan memeriksanya.']);

        return back();
    }
}
