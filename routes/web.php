<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ClassCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/koleksi-buku', [BookController::class, 'index'])->name('catalog.index');
    Route::get('/transaksi', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/transaksi/{loan}/pengembalian', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('/transaksi/{loan}/pengembalian/approve', [LoanController::class, 'approveReturn'])->name('loans.return.approve');
    Route::post('/transaksi/{loan}/pengembalian/reject', [LoanController::class, 'rejectReturn'])->name('loans.return.reject');
    Route::post('/transaksi/{loan}/denda/bayar', [LoanController::class, 'payFine'])->name('loans.fine.pay');
    Route::post('/transaksi/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/transaksi/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');

    Route::middleware('role:member')->group(function () {
        Route::get('/peminjaman', [LoanController::class, 'borrowForm'])->name('loans.borrow-form');
        Route::post('/peminjaman', [LoanController::class, 'store'])->name('loans.borrow-store');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/aktivitas-admin', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::resource('class-categories', ClassCategoryController::class)->except(['show']);
        Route::resource('books', BookController::class)->except(['show', 'index']);
        Route::resource('members', MemberController::class)->except(['show'])->parameter('members', 'member');
        Route::resource('loans', LoanController::class)->except(['index', 'show']);
    });
});
