<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\DraftBillApiController;
use App\Http\Controllers\Api\ListOfOrdersController;

// Use web middleware for session authentication
Route::middleware(['web'])->group(function () {
    
    // Customer routes
    Route::get('/customers/search', [CustomerApiController::class, 'search']);
    Route::get('/customers/{id}', [CustomerApiController::class, 'show']);

    // Product routes
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/company/info', [ProductApiController::class, 'getCompanyInfo']);

    // Draft bill routes
    Route::post('/draft-invoices', [DraftBillApiController::class, 'store']);
    Route::get('/draft-invoices', [DraftBillApiController::class, 'index']);
    Route::get('/draft-invoices/{id}', [DraftBillApiController::class, 'show']);
    Route::delete('/draft-invoices/{id}', [DraftBillApiController::class, 'destroy']);
    Route::post('/draft-invoices/{id}/pay', [DraftBillApiController::class, 'processPayment']);
    Route::get('/draft-invoices/{id}/receipt', [DraftBillApiController::class, 'getReceipt']);

    // Order routes
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::post('/orders/draft', [OrderApiController::class, 'storeDraft']);
    Route::get('/orders/drafts', [OrderApiController::class, 'getDrafts']);
    Route::post('/orders/{id}/convert', [OrderApiController::class, 'convertDraftToOrder']);
    Route::get('/orders/today/summary', [OrderApiController::class, 'todaySummary']);
    Route::delete('/orders/{id}', [OrderApiController::class, 'destroy']);

    // Order management routes
    Route::get('/orders', [ListOfOrdersController::class, 'index']);
    Route::get('/orders/{id}', [ListOfOrdersController::class, 'show']);
    Route::get('/orders/{id}/payments', [ListOfOrdersController::class, 'getPayments']);
    Route::get('/orders/{id}/receipt', [ListOfOrdersController::class, 'getReceipt']);
    Route::put('/orders/{id}/status', [ListOfOrdersController::class, 'updateStatus']);

    // Return order route
    Route::post('/orders/return', [ReturnOrderController::class, 'processReturn']);
    Route::get('/orders/{id}/returns', [ReturnOrderController::class, 'getReturnHistory']);
});

// Checkout route
Route::post('/checkout', function (Request $request) {
    Log::info('Checkout data', $request->all());
    return response()->json(['success' => true]);
});

// Categories API
Route::get('/categories', function () {
    $categories = \App\Models\Category::all();
    return response()->json($categories);
});

// Categories API
Route::get('/categories', function () {
    try {
        $categories = \App\Models\Category::all();
        return response()->json($categories);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch categories'], 500);
    }
});

// Products API with category filter
Route::get('/products', function (Request $request) {
    try {
        $query = \App\Models\InventoryItem::where('available_quantity', '>', 0);
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->get()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'item_code' => $item->item_code,
                'selling_price' => $item->selling_price,
                'market_price' => $item->market_price,
                'available_quantity' => $item->available_quantity,
                'image' => $item->image,
                'category_id' => $item->category_id,
                'cost' => $item->cost
            ];
        });
        
        return response()->json($products);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch products'], 500);
    }
});