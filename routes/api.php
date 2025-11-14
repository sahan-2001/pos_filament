<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\DraftBillApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Order routes
Route::post('/orders', [OrderApiController::class, 'store']);
Route::post('/orders/draft', [OrderApiController::class, 'storeDraft']);
Route::get('/orders/drafts', [OrderApiController::class, 'getDrafts']);
Route::post('/orders/{id}/convert', [OrderApiController::class, 'convertDraftToOrder']);
Route::get('/orders', [OrderApiController::class, 'index']);
Route::get('/orders/{id}', [OrderApiController::class, 'show']);
Route::put('/orders/{id}/status', [OrderApiController::class, 'updateStatus']);
Route::get('/orders/today/summary', [OrderApiController::class, 'todaySummary']);
Route::delete('/orders/{id}', [OrderApiController::class, 'destroy']);
Route::get('/orders/{id}/receipt', [OrderApiController::class, 'getReceipt']);

// Draft bull routes
Route::post('/draft-invoices', [DraftBillApiController::class, 'store']);
Route::get('/draft-invoices', [DraftBillApiController::class, 'index']);
Route::get('/draft-invoices/{id}', [DraftBillApiController::class, 'show']);
Route::delete('/draft-invoices/{id}', [DraftBillApiController::class, 'destroy']);
Route::post('/draft-invoices/{id}/pay', [DraftBillApiController::class, 'processPayment']);
Route::get('/draft-invoices/{id}/receipt', [DraftBillApiController::class, 'getReceipt']);

Route::post('/checkout', function (Request $request) {
    Log::info('Checkout data', $request->all());
    return response()->json(['success' => true]);
});

Route::get('/customers/search', [CustomerApiController::class, 'search']);
Route::get('/customers/{id}', [CustomerApiController::class, 'show']);

Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/orders/{id}/receipt', [OrderApiController::class, 'getReceipt']);
