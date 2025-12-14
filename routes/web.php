<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BillingController::class, 'showForm'])->name('billing.form');
Route::post('/billing/generate', [BillingController::class, 'generateBill'])->name('billing.generate');
Route::get('/billing/{invoice}', [BillingController::class, 'displayBill'])->name('billing.display');
Route::get('/billing/{invoice}/pdf', [BillingController::class, 'downloadPdf'])->name('billing.pdf');
Route::get('/purchase-history', [BillingController::class, 'purchaseHistory'])->name('billing.history');

// Database Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard/customers', [DashboardController::class, 'customers'])->name('dashboard.customers');
Route::get('/dashboard/products', [DashboardController::class, 'products'])->name('dashboard.products');
Route::get('/dashboard/invoices', [DashboardController::class, 'invoices'])->name('dashboard.invoices');
