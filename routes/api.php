<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\DraftInvoiceController;
use App\Http\Controllers\Api\DraftBillApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/checkout', function (Request $request) {
    // Save cart items to database or handle payment logic
    Log::info('Checkout data', $request->all());
    return response()->json(['success' => true]);
});

Route::get('/customers/search', [CustomerApiController::class, 'search']);
Route::get('/customers/{id}', [CustomerApiController::class, 'show']);

Route::get('/products', [ProductApiController::class, 'index']);
Route::post('/draft-invoices', [DraftInvoiceController::class, 'store']);
Route::get('/draft-invoices', [DraftBillApiController::class, 'index']);
Route::get('/draft-invoices/{id}', [DraftBillApiController::class, 'show']);