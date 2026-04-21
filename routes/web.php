<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Customer Management
    Route::middleware('role:admin,branch_manager,teller')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('/customers/{customer}/details', [CustomerController::class, 'getDetails'])->name('customers.details');
    });
    
    // Transaction Management
    Route::middleware('role:admin,branch_manager,teller')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::patch('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::get('/transactions/details/{pawnTicketNumber}', [TransactionController::class, 'getDetails'])->name('transactions.details');

        // Pawn Transaction Routes
        Route::get('/transactions/pawn/create', [TransactionController::class, 'createPawn'])->name('transactions.createPawn');
        Route::post('/transactions/pawn', [TransactionController::class, 'storePawn'])->name('transactions.storePawn');

        // Renewal Routes
        Route::get('/transactions/{transaction}/renewal', [TransactionController::class, 'createRenewal'])->name('transactions.createRenewal');
        Route::post('/transactions/{transaction}/renewal', [TransactionController::class, 'storeRenewal'])->name('transactions.storeRenewal');

        // Redemption Routes
        Route::get('/transactions/{transaction}/redemption', [TransactionController::class, 'createRedemption'])->name('transactions.createRedemption');
        Route::post('/transactions/{transaction}/redemption', [TransactionController::class, 'storeRedemption'])->name('transactions.storeRedemption');
    });
    
    // Payment Management
    Route::middleware('role:admin,branch_manager,cashier')->group(function () {
        Route::resource('payments', PaymentController::class);
        Route::get('/payments/{payment}/details', [PaymentController::class, 'getDetails'])->name('payments.details');
    });
    
    // Reports
    Route::middleware('role:admin,branch_manager,auditor')->group(function () {
        Route::get('/reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
        Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('/reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
        Route::get('/reports/financial-summary', [ReportController::class, 'financialSummary'])->name('reports.financial-summary');
    });

    Route::middleware('role:admin,auditor')->group(function () {
        Route::get('/reports/audit-logs', [ReportController::class, 'auditLogs'])->name('reports.audit-logs');
        Route::get('/reports/export-transactions', [ReportController::class, 'exportTransactions'])->name('reports.export-transactions');
    });

    // Admin only routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';

