<?php

namespace App\Mail;

use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Tells the admin that a user reported wrong data about a shop.
 */
class NewReport extends Mailable
{
    public function __construct(public Report $report) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Laporan data baru: {$this->report->shop->name}");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-report',
            with: ['reviewUrl' => ReportResource::getUrl('edit', ['record' => $this->report], panel: 'admin')],
        );
    }
}
