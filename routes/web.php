<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\DirectoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DirectoryController::class, 'index'])->name('home');
Route::get('/shops/{shop:slug}', [DirectoryController::class, 'show'])->name('directory.show');
Route::get('/agent', [AgentController::class, 'index'])->name('agent.index');
Route::post('/agent', [AgentController::class, 'parse'])->name('agent.parse');
Route::post('/shops/nearby', [DirectoryController::class, 'nearby'])->name('directory.nearby');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
