<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::middleware(['can:isAdmin'])->group(function () {
        Route::resource('books', BookController::class);
    });

    // Shared Routes (Admin & Petugas)
    Route::resource('members', \App\Http\Controllers\MemberController::class);

    // Shared Routes (View Loans)
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/report', [LoanController::class, 'report'])->name('loans.report');

    // Petugas Routes
    Route::middleware(['can:isPetugas'])->group(function () {
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    });
});
