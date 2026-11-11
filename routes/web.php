<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/tempat', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/shops/{shop:slug}', [DirectoryController::class, 'show'])->name('directory.show');
Route::post('/shops/nearby', [DirectoryController::class, 'nearby'])->name('directory.nearby');
Route::get('/bandingkan', [CompareController::class, 'index'])->name('compare.index');
Route::get('/agent', [AgentController::class, 'index'])->name('agent.index');
Route::post('/agent', [AgentController::class, 'parse'])->name('agent.parse');

Route::inertia('/cara-kerja', 'info/how-it-works')->name('info.how-it-works');
Route::inertia('/tentang', 'info/about')->name('info.about');
Route::inertia('/pertanyaan', 'info/faq')->name('info.faq');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
