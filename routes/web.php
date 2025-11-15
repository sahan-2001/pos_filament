<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderPdfController;
use App\Filament\Resources\ActivityLogResource;
use Filament\Facades\Filament; 
use App\Models\CustomerOrder;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\SampleOrderPdfController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PoFrontendController;
use App\Http\Controllers\SOFrontendController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CuttingRecordPrintController;
use App\Http\Controllers\RegisterArrivalPrintController;
use App\Http\Controllers\CuttingLabelPrintController;
use App\Http\Controllers\PerformanceRecordPrintController;
use App\Http\Controllers\PerformanceRecordViewController;
use App\Http\Controllers\SupplierAdvanceInvoiceController;
use App\Http\Controllers\EndOfDayReportPdfController;
use App\Http\Controllers\ReleaseMaterialPrintController;
use App\Http\Controllers\POSController;
use App\Models\SupplierAdvanceInvoice;
use Illuminate\Http\Request;  
use App\Models\SuppAdvInvoicePayment;
use App\Models\PurchaseOrderInvoice;
use App\Models\PoInvoicePayment;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', [PageController::class, 'index'])->name('welcome');

Route::get('/purchase-order/{purchase_order}/pdf', [PurchaseOrderPdfController::class, 'show'])->name('purchase-order.pdf');
Route::get('/sample-orders/{sample_order}/pdf', [SampleOrderPdfController::class, 'show'])->name('sample-orders.pdf');
Route::get('/cutting-records/{cutting_record}/print', [CuttingRecordPrintController::class, 'print'])->name('cutting-records.print');
Route::get('/register-arrivals/{registerArrival}/print', [RegisterArrivalPrintController::class, 'print'])->name('register-arrivals.print');
Route::get('/cutting-records/{cuttingRecord}/print-labels', [CuttingLabelPrintController::class, 'print'])->name('cutting-records.print-labels');
Route::get('/performance-records/{enter_performance_record}/print', [PerformanceRecordPrintController::class, 'print'])->name('performance-records.print');


Route::middleware(['auth', 'verified'])->group(function () {
    ActivityLogResource::routes(Filament::getCurrentPanel());
});

Route::get('/admin/enter-performance-record/{id}/view', [PerformanceRecordViewController::class, 'show'])
    ->name('filament.resources.enter-performance-record.view');

// Supplier Advance Invoice PDF Route
Route::get('/supplier-advance-invoices/{supplier_advance_invoice}/pdf', 
    [SupplierAdvanceInvoiceController::class, 'show'])
    ->name('supplier-advance-invoices.pdf');

// PO final Invoice PDF Route
Route::get('/purchase-order-invoice/{purchase_order_invoice}/pdf', 
    [\App\Http\Controllers\PurchaseOrderFinalPdfController::class, 'show'])
    ->name('purchase-order-invoice.pdf');


// Advance payment data
Route::get('/supplier-advance-invoices/{invoice}/payment-receipt', function (
    SupplierAdvanceInvoice $invoice,
    Request $request
) {
    // Load the specific payment if payment ID is provided
    if ($request->has('payment')) {
        $payment = SuppAdvInvoicePayment::findOrFail($request->payment);
    } else {
        // Fallback to loading all payments
        $invoice->load('payments');
        $payment = null;
    }

    $pdf = Pdf::loadView('pdf.supplier-advance-payment', [
        'invoice' => $invoice,
        'payment' => $payment
    ]);

    return $pdf->stream('Payment-Receipt-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) . '.pdf');
})->name('supplier-advance.payment-receipt');


// Final Invoice payment
Route::get('/purchase-order-invoices/{invoice}/payment-receipt', function (
    PurchaseOrderInvoice $invoice,
    Request $request
) {
    if ($request->has('payment')) {
        $payment = PoInvoicePayment::findOrFail($request->payment);
    } else {
        $invoice->load('payments');
        $payment = null;
    }

    $pdf = Pdf::loadView('pdf.purchase-order-invoice-payment', [
        'invoice' => $invoice,
        'payment' => $payment,
    ]);

    return $pdf->stream('POI-Payment-Receipt-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) . '.pdf');
})->name('purchase-order-invoice.payment-receipt');

// End of day reporting route
Route::get('/end-of-day-report/{endOfDayReport}/pdf', [EndOfDayReportPdfController::class, 'show'])
    ->name('end-of-day-reports.pdf');


// Release material report route 
Route::get('/release-materials/{releaseMaterial}/print', [ReleaseMaterialPrintController::class, 'print'])
    ->name('release-materials.print');

// Frontend route
Route::get('/purchase-order/{id}/{random_code}', [POFrontendController::class, 'showPurchaseOrder'])
    ->name('purchase-order.show');

Route::get('/sample-orders/{id}/{random_code}', [SOFrontendController::class, 'showSampleOrder'])
    ->name('sample-orders.show');

// POS route - UPDATED to use auth:sanctum to match API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
});
Route::view('/draft-bills', 'pos.draft-bills');
Route::view('/orders', 'pos.orders');

