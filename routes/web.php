<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SparePartsController;
use App\Http\Controllers\UserManagementController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth routes (simple session-based)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot.post');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('password.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.post');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// Test email route (remove in production)
Route::get('/test-email', function () {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from your Laravel application!', function ($message) {
        $message->to('your-email@example.com')->subject('Test Email from Laravel');
    });
    return 'Test email sent! Check your inbox or logs.';
});

// Protected dashboard routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

    // Customer CRUD routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Quotation CRUD routes
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{id}/details', [QuotationController::class, 'details'])->name('quotations.details');
    Route::get('/quotations/api/view', [QuotationController::class, 'apiView'])->name('quotations.api-view');
    Route::post('/quotations/{id}/generate-pdf', [QuotationController::class, 'generatePdf'])->name('quotations.generate-pdf');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');

    // Spare Parts CRUD routes
    Route::middleware('admin')->group(function () {
        Route::get('/spare-parts', [SparePartsController::class, 'index'])->name('spare-parts.index');
        Route::get('/spare-parts/create', [SparePartsController::class, 'create'])->name('spare-parts.create');
        Route::post('/spare-parts', [SparePartsController::class, 'store'])->name('spare-parts.store');
        Route::get('/spare-parts/{sparePart}/edit', [SparePartsController::class, 'edit'])->name('spare-parts.edit');
        Route::put('/spare-parts/{sparePart}', [SparePartsController::class, 'update'])->name('spare-parts.update');
        Route::delete('/spare-parts/{sparePart}', [SparePartsController::class, 'destroy'])->name('spare-parts.destroy');
        Route::post('/spare-parts/import', [SparePartsController::class, 'import'])->name('spare-parts.import');

        // User Management routes
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management.index');
        Route::put('/user-management/{user}/role', [UserManagementController::class, 'updateRole'])->name('user-management.update-role');
    });
});
