<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\DraftBillApiController;
use App\Http\Controllers\Api\ListOfOrdersController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Customer routes
Route::get('/customers/search', [CustomerApiController::class, 'search']);
Route::get('/customers/{id}', [CustomerApiController::class, 'show']);

// Product routes
Route::get('/products', [ProductApiController::class, 'index']);

// Draft bill routes
Route::post('/draft-invoices', [DraftBillApiController::class, 'store']);
Route::get('/draft-invoices', [DraftBillApiController::class, 'index']);
Route::get('/draft-invoices/{id}', [DraftBillApiController::class, 'show']);
Route::delete('/draft-invoices/{id}', [DraftBillApiController::class, 'destroy']);
Route::post('/draft-invoices/{id}/pay', [DraftBillApiController::class, 'processPayment']);
Route::get('/draft-invoices/{id}/receipt', [DraftBillApiController::class, 'getReceipt']);

// Order routes - Using ListOfOrdersController for listing and management
Route::middleware('auth:sanctum')->group(function () {
    // Order listing and management
    Route::get('/orders', [ListOfOrdersController::class, 'index']);
    Route::get('/orders/{id}', [ListOfOrdersController::class, 'show']);
    Route::get('/orders/{id}/payments', [ListOfOrdersController::class, 'getPayments']);
    Route::get('/orders/{id}/receipt', [ListOfOrdersController::class, 'getReceipt']);
    Route::put('/orders/{id}/status', [ListOfOrdersController::class, 'updateStatus']);
    
    // Order creation (keep using OrderApiController for creating orders)
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::post('/orders/draft', [OrderApiController::class, 'storeDraft']);
    Route::get('/orders/drafts', [OrderApiController::class, 'getDrafts']);
    Route::post('/orders/{id}/convert', [OrderApiController::class, 'convertDraftToOrder']);
    Route::get('/orders/today/summary', [OrderApiController::class, 'todaySummary']);
    Route::delete('/orders/{id}', [OrderApiController::class, 'destroy']);
});

// Checkout route
Route::post('/checkout', function (Request $request) {
    Log::info('Checkout data', $request->all());
    return response()->json(['success' => true]);
});