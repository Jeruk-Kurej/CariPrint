<?php

namespace App\Services\Reports;

use App\Mail\NewReport;
use App\Models\Report;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Emails the admins when a report arrives. A failed email is reported but never blocks the user's request,
 * because the report itself is already stored.
 */
class ReportMailer
{
    public function notifyAdmins(Report $report): void
    {
        $recipients = config('cariprint.admin.notify_emails');

        if ($recipients === []) {
            return;
        }

        try {
            Mail::to($recipients)->send(new NewReport($report));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
