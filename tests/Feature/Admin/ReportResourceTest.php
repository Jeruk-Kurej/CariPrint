<?php

use App\Enums\ReportStatus;
use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->actingAs(User::factory()->admin()->create()));

test('the report list page opens for an admin', function () {
    $this->get('/admin/reports')->assertOk()->assertSee('Laporan data');
});

test('an admin sees the reports and can filter by status', function () {
    $waiting = Report::factory()->create();
    $done = Report::factory()->create(['status' => ReportStatus::Resolved]);

    Livewire::test(ListReports::class)
        ->assertCanSeeTableRecords([$waiting, $done])
        ->filterTable('status', ReportStatus::Pending->value)
        ->assertCanSeeTableRecords([$waiting])
        ->assertCanNotSeeTableRecords([$done]);
});

test('the review page shows the shop, the reporter, and the message', function () {
    $report = Report::factory()->create(['message' => 'Nomornya sudah ganti.']);

    Livewire::test(EditReport::class, ['record' => $report->getRouteKey()])
        ->assertFormSet([
            'shop_name' => $report->shop->name,
            'reporter_email' => $report->user->email,
            'message' => 'Nomornya sudah ganti.',
        ]);
});

test('deciding a report keeps the note and stamps the time once', function () {
    $report = Report::factory()->create();

    Livewire::test(EditReport::class, ['record' => $report->getRouteKey()])
        ->fillForm(['status' => ReportStatus::Resolved->value, 'admin_note' => 'Sudah diperbaiki, terima kasih.'])
        ->call('save')
        ->assertHasNoFormErrors();

    $report->refresh();
    $firstTime = $report->resolved_at;

    expect($report->status)->toBe(ReportStatus::Resolved)
        ->and($report->admin_note)->toBe('Sudah diperbaiki, terima kasih.')
        ->and($firstTime)->not->toBeNull();

    $this->travel(2)->hours();

    Livewire::test(EditReport::class, ['record' => $report->getRouteKey()])
        ->fillForm(['status' => ReportStatus::Rejected->value])
        ->call('save');

    expect($report->fresh()->resolved_at?->equalTo($firstTime))->toBeTrue();
});

test('moving a report back to pending clears the decision time', function () {
    $report = Report::factory()->create(['status' => ReportStatus::Resolved, 'resolved_at' => now()]);

    Livewire::test(EditReport::class, ['record' => $report->getRouteKey()])
        ->fillForm(['status' => ReportStatus::Pending->value])
        ->call('save');

    expect($report->fresh()->resolved_at)->toBeNull();
});

test('the menu shows how many reports wait', function () {
    Report::factory()->count(2)->create();
    Report::factory()->create(['status' => ReportStatus::Rejected]);

    expect(ReportResource::getNavigationBadge())->toBe('2');
});

test('a plain user cannot open the report list', function () {
    auth()->logout();
    $this->actingAs(User::factory()->create())->get('/admin/reports')->assertForbidden();
});
