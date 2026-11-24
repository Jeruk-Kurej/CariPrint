<?php

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Mail\NewReport;
use App\Models\Report;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

function reportPayload(array $overrides = []): array
{
    return ['category' => 'hours', 'message' => 'Hari Sabtu tutupnya jam 15.00, bukan jam 16.00.', ...$overrides];
}

test('a guest cannot report and is sent to sign in', function () {
    $shop = Shop::factory()->create();

    $this->post("/shops/{$shop->slug}/laporan", reportPayload())->assertRedirect('/login');

    expect(Report::count())->toBe(0);
});

test('a signed-in user can report wrong data about a published shop', function () {
    Mail::fake();
    $user = User::factory()->create();
    $shop = Shop::factory()->create();

    $this->actingAs($user)->from("/shops/{$shop->slug}")
        ->post("/shops/{$shop->slug}/laporan", reportPayload())
        ->assertSessionHasNoErrors()
        ->assertRedirect("/shops/{$shop->slug}");

    $report = Report::sole();

    expect($report->user_id)->toBe($user->id)
        ->and($report->shop_id)->toBe($shop->id)
        ->and($report->category)->toBe(ReportCategory::Hours)
        ->and($report->status)->toBe(ReportStatus::Pending)
        ->and($report->message)->toBe('Hari Sabtu tutupnya jam 15.00, bukan jam 16.00.');
});

test('the admins are emailed about a new report', function () {
    Mail::fake();
    config(['cariprint.admin.notify_emails' => ['admin@example.com']]);
    $shop = Shop::factory()->create(['name' => 'Print Sejahtera']);

    $this->actingAs(User::factory()->create())->post("/shops/{$shop->slug}/laporan", reportPayload());

    Mail::assertSent(NewReport::class, fn (NewReport $mail) => $mail->hasTo('admin@example.com')
        && str_contains($mail->envelope()->subject, 'Print Sejahtera'));
});

test('a failing email does not lose the report', function () {
    config(['cariprint.admin.notify_emails' => ['admin@example.com']]);
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));
    $shop = Shop::factory()->create();

    $this->actingAs(User::factory()->create())->post("/shops/{$shop->slug}/laporan", reportPayload())
        ->assertSessionHasNoErrors();

    expect(Report::count())->toBe(1);
});

test('a draft shop cannot be reported', function () {
    $shop = Shop::factory()->draft()->create();

    $this->actingAs(User::factory()->create())->post("/shops/{$shop->slug}/laporan", reportPayload())->assertNotFound();

    expect(Report::count())->toBe(0);
});

test('the category and message are validated', function (array $overrides, string $field) {
    $shop = Shop::factory()->create();

    $this->actingAs(User::factory()->create())->post("/shops/{$shop->slug}/laporan", reportPayload($overrides))
        ->assertSessionHasErrors($field);

    expect(Report::count())->toBe(0);
})->with([
    'missing category' => [['category' => ''], 'category'],
    'unknown category' => [['category' => 'rahasia'], 'category'],
    'empty message' => [['message' => ''], 'message'],
    'too short' => [['message' => 'salah'], 'message'],
    'too long' => [['message' => str_repeat('a', 501)], 'message'],
]);

test('the validation messages are in Indonesian', function () {
    $shop = Shop::factory()->create();

    $this->actingAs(User::factory()->create())->post("/shops/{$shop->slug}/laporan", reportPayload(['category' => '', 'message' => 'salah']))
        ->assertSessionHasErrors([
            'category' => 'Pilih jenis masalahnya dulu.',
            'message' => 'Ceritakan sedikit lebih jelas, minimal 10 karakter.',
        ]);
});

test('a user cannot file a second report on a shop while the first one waits', function () {
    $user = User::factory()->create();
    $shop = Shop::factory()->create();
    Report::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id]);

    $this->actingAs($user)->post("/shops/{$shop->slug}/laporan", reportPayload())
        ->assertSessionHasErrors(['category' => 'Laporanmu untuk tempat ini masih ditinjau admin. Tunggu hasilnya dulu.']);

    expect(Report::count())->toBe(1);
});

test('a user can report again once the earlier report was decided, and on another shop', function () {
    $user = User::factory()->create();
    $shop = Shop::factory()->create();
    $other = Shop::factory()->create();
    Report::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id, 'status' => ReportStatus::Resolved]);
    Report::factory()->create(['user_id' => $user->id, 'shop_id' => $other->id]);

    $this->actingAs($user)->post("/shops/{$shop->slug}/laporan", reportPayload())->assertSessionHasNoErrors();

    expect(Report::where('shop_id', $shop->id)->count())->toBe(2);
});

test('reports are limited to a few per hour', function () {
    $user = User::factory()->create();

    foreach (Shop::factory()->count(5)->create() as $shop) {
        $this->actingAs($user)->post("/shops/{$shop->slug}/laporan", reportPayload())->assertSessionHasNoErrors();
    }

    $last = Shop::factory()->create();
    $this->actingAs($user)->post("/shops/{$last->slug}/laporan", reportPayload())->assertStatus(429);
});

test('the shop page tells a guest nothing private and a user whether a report is waiting', function () {
    $shop = Shop::factory()->create();
    $user = User::factory()->create();

    $this->get("/shops/{$shop->slug}")->assertInertia(fn (Assert $page) => $page
        ->where('reporting.hasPending', false)
        ->has('reporting.categories', count(ReportCategory::cases())));

    Report::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id]);

    $this->actingAs($user)->get("/shops/{$shop->slug}")
        ->assertInertia(fn (Assert $page) => $page->where('reporting.hasPending', true));
});

test('a guest who opens a shop page returns to it after signing in', function () {
    $shop = Shop::factory()->create();

    $this->get("/shops/{$shop->slug}")->assertSessionHas('url.intended', url("/shops/{$shop->slug}"));
});

test('the account page lists only the reports of the signed-in user, newest first', function () {
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['name' => 'Print Sejahtera']);
    Report::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id, 'message' => 'Laporan lama', 'created_at' => now()->subDay(), 'status' => ReportStatus::Resolved, 'admin_note' => 'Sudah diperbaiki']);
    Report::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id, 'message' => 'Laporan baru']);
    Report::factory()->create(['message' => 'Milik orang lain']);

    $this->actingAs($user)->get('/akun')->assertInertia(fn (Assert $page) => $page
        ->has('reports', 2)
        ->where('reports.0.message', 'Laporan baru')
        ->where('reports.0.statusLabel', 'Menunggu')
        ->where('reports.1.statusLabel', 'Selesai')
        ->where('reports.1.adminNote', 'Sudah diperbaiki')
        ->where('reports.1.shopName', 'Print Sejahtera'));
});

test('a report always belongs to the signed-in user and starts as pending', function () {
    Mail::fake();
    $user = User::factory()->create();
    $other = User::factory()->create();
    $shop = Shop::factory()->create();

    $this->actingAs($user)->post("/shops/{$shop->slug}/laporan", reportPayload([
        'user_id' => $other->id,
        'status' => ReportStatus::Resolved->value,
        'admin_note' => 'Disetujui sendiri',
    ]));

    $report = Report::sole();

    expect($report->user_id)->toBe($user->id)
        ->and($report->status)->toBe(ReportStatus::Pending)
        ->and($report->admin_note)->toBeNull();
});
