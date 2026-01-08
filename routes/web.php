<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\FinePaymentController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::middleware(['can:isAdmin'])->group(function () {
        Route::resource('books', BookController::class);
        Route::put('/books/{book}/update-stock', [BookController::class, 'updateStock'])->name('books.update-stock');
        Route::get('/api/books/preview-code', [BookController::class, 'getPreviewCode'])->name('api.books.preview-code');
    });

    // Shared Routes (Admin & Petugas)
    Route::resource('members', \App\Http\Controllers\MemberController::class);

    // Shared Routes (View Loans)
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/report', [LoanController::class, 'report'])->name('loans.report');
    Route::get('/api/user-loans/{userId}', [LoanController::class, 'getUserLoans'])->name('api.user-loans');

    // Petugas & Admin Routes (Admin bisa akses semua fitur petugas)
    Route::middleware(['can:isPetugasOrAdmin'])->group(function () {
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
        Route::post('/loans/{loan}/renew', [LoanController::class, 'renew'])->name('loans.renew');
        
        // Fine Payment Routes
        Route::get('/fine-payments', [FinePaymentController::class, 'index'])->name('fine-payments.index');
        Route::get('/fine-payments/create/{loan}', [FinePaymentController::class, 'create'])->name('fine-payments.create');
        Route::post('/fine-payments/{loan}', [FinePaymentController::class, 'store'])->name('fine-payments.store');
    });
});
