<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ShopSuggestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/tempat', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/shops/{shop:slug}', [DirectoryController::class, 'show'])->name('directory.show');
Route::post('/shops/nearby', [DirectoryController::class, 'nearby'])->name('directory.nearby');
Route::get('/bandingkan', [CompareController::class, 'index'])->name('compare.index');
Route::get('/agent', [AgentController::class, 'index'])->name('agent.index');
Route::post('/agent', [AgentController::class, 'parse'])->name('agent.parse');

Route::get('/masuk/google', [GoogleLoginController::class, 'redirect'])->middleware('guest')->name('google.redirect');
Route::get('/masuk/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/usul-tempat', [ShopSuggestionController::class, 'create'])->name('suggestions.create');
    Route::post('/usul-tempat', [ShopSuggestionController::class, 'store'])->middleware('throttle:5,60')->name('suggestions.store');
});

Route::inertia('/cara-kerja', 'info/how-it-works')->name('info.how-it-works');
Route::inertia('/tentang', 'info/about')->name('info.about');
Route::inertia('/pertanyaan', 'info/faq')->name('info.faq');
Route::get('/privasi', PrivacyController::class)->name('info.privacy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
