<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PuzzleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/puzzle', [PuzzleController::class, 'show'])->name('puzzle.show');
    Route::post('/submit', [PuzzleController::class, 'submit'])->name('puzzle.submit');
    Route::post('/puzzle/end', [PuzzleController::class, 'end'])->name('puzzle.end');
});

require __DIR__.'/auth.php';
