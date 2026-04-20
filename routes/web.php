<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PenanggungJawabController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Auth;

/**
 * Authentication Routes
 */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register']);

/**
 * Homepage
 */
Route::get('/', function () {
    return view('landing');
})->name('home');

/**
 * Storage Routes (serve files from storage when symlink doesn't work)
 */
Route::get('/storage/covers/{filename}', [StorageController::class, 'serveCover'])->name('storage.cover');

/**
 * Dashboard Routes (dengan perbedaan role)
 */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Alias untuk member dashboard
    Route::get('/dashboard-member', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.member');

    // Alias untuk admin dashboard
    Route::get('/dashboard-admin', function () {
        return view('dashboard');
    })->name('dashboard.admin');
});

/**
 * Protected routes (require login)
 */
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::resource('loans', LoanController::class);
    Route::get('loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.returnForm');
    Route::post('loans/{loan}/process-return', [LoanController::class, 'processReturn'])->name('loans.processReturn');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::patch('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::resource('books', BookController::class)->except(['index', 'show']);
    Route::resource('members', MemberController::class)->except(['index', 'show']);
    Route::resource('penanggung-jawab', PenanggungJawabController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::resource('books', BookController::class)->only(['index', 'show']);
    Route::resource('members', MemberController::class)->only(['index', 'show']);
    // Route::resource('books', BookController::class)->except(['index', 'show']); // Disabled - CRUD restricted to admins via middleware above
    Route::resource('members', MemberController::class)->except(['index', 'show']);
});

/**
 * Routes untuk peminjaman dan pengembalian
 */
Route::resource('loans', LoanController::class);
Route::get('loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.returnForm');
Route::post('loans/{loan}/process-return', [LoanController::class, 'processReturn'])->name('loans.processReturn');
Route::get('loans/{loan}/confirm-return', [LoanController::class, 'confirmReturn'])->name('loans.confirmReturn');
Route::post('loans/{loan}/process-confirm-return', [LoanController::class, 'processConfirmReturn'])->name('loans.processConfirmReturn');

/**
 * Report Routes (Admin Only)
 */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/reports/loans', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/loans/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/loans/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/loans/export-html', [ReportController::class, 'exportHtml'])->name('reports.export-html');
    Route::get('/reports/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');
});
