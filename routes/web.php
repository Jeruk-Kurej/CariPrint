<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\FileCheckController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopSearchController;
use App\Http\Controllers\ShopSuggestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/tempat', [DirectoryController::class, 'index'])->name('directory.index');
Route::post('/tempat/cari-ai', ShopSearchController::class)->name('directory.ai-search');
Route::get('/shops/{shop:slug}', [DirectoryController::class, 'show'])->name('directory.show');
Route::post('/shops/nearby', [DirectoryController::class, 'nearby'])->name('directory.nearby');
Route::get('/bandingkan', [CompareController::class, 'index'])->name('compare.index');
Route::get('/agent', [AgentController::class, 'index'])->name('agent.index');
Route::post('/agent', [AgentController::class, 'parse'])->name('agent.parse');
Route::post('/agent/cek-warna', FileCheckController::class)->middleware('throttle:60,1')->name('agent.file-check');
Route::post('/agent/hitung-ulang', [AgentController::class, 'recalculate'])->middleware('throttle:60,1')->name('agent.recalculate');

Route::get('/masuk/google', [GoogleLoginController::class, 'redirect'])->middleware('guest')->name('google.redirect');
Route::get('/masuk/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');

Route::get('/usul-tempat', [ShopSuggestionController::class, 'create'])->name('suggestions.create');

Route::middleware('auth')->group(function () {
    Route::post('/usul-tempat', [ShopSuggestionController::class, 'store'])->middleware('throttle:5,60')->name('suggestions.store');
    Route::post('/shops/{shop:slug}/laporan', [ReportController::class, 'store'])->middleware('throttle:5,60')->name('reports.store');
});

Route::inertia('/cara-kerja', 'info/how-it-works')->name('info.how-it-works');
Route::inertia('/tentang', 'info/about')->name('info.about');
Route::inertia('/pertanyaan', 'info/faq')->name('info.faq');
Route::get('/privasi', PrivacyController::class)->name('info.privacy');
Route::get('/hubungi-kami', [ContactController::class, 'create'])->name('contact.create');
Route::post('/hubungi-kami', [ContactController::class, 'store'])->middleware('throttle:5,60')->name('contact.store');

Route::middleware('auth')->group(function () {
    Route::get('/akun', AccountController::class)->name('account');
    Route::redirect('/dashboard', '/akun')->name('dashboard');
});

require __DIR__.'/settings.php';
