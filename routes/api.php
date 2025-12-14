<?php

use App\Http\Controllers\Api\InsightsController;
use App\Http\Controllers\BillingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/customer-by-email', [BillingController::class, 'getCustomerByEmail']);
Route::get('/product/{id}', [BillingController::class, 'getProduct']);

Route::prefix('insights')->group(function () {
    Route::get('/high-variety-customers', [InsightsController::class, 'highVarietyCustomers']);
    Route::get('/stock-forecast', [InsightsController::class, 'stockForecast']);
    Route::get('/repeat-customers', [InsightsController::class, 'repeatCustomerInsights']);
    Route::get('/high-demand-orders', [InsightsController::class, 'highDemandOrders']);
});
