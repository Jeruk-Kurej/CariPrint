<?php

use App\Http\Controllers\DirectoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DirectoryController::class, 'index'])->name('home');
Route::get('/shops/{shop:slug}', [DirectoryController::class, 'show'])->name('directory.show');
Route::post('/shops/nearby', [DirectoryController::class, 'nearby'])->name('directory.nearby');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
