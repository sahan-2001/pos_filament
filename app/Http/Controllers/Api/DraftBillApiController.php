<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DraftInvoice;
use App\Models\DraftInvoiceItem;
use App\Models\DraftBillPayment;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DraftBillApiController extends Controller
{
    // GET /api/draft-invoices
    public function index(Request $request)
    {
        $status = $request->get('status');
        $date = $request->get('date'); // format: YYYY-MM-DD

        $query = DraftInvoice::with('customer', 'items');

        if ($status) {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $drafts = $query->orderByDesc('created_at')->get()->map(function ($draft) {
            return [
                'id' => $draft->id,
                'created_at' => $draft->created_at,
                'customer_id' => $draft->customer_id,
                'customer_name' => $draft->customer ? $draft->customer->name : null,
                'total' => $draft->total,
                'discount' => $draft->discount,
                'status' => $draft->status,
                'items_count' => $draft->items ? $draft->items->count() : 0,
            ];
        });

        return response()->json($drafts);
    }

    // GET /api/draft-invoices/{id}
    public function show($id)
    {
        $draft = DraftInvoice::with(['customer', 'items.inventoryItem'])->findOrFail($id);

        return response()->json([
            'id' => $draft->id,
            'created_at' => $draft->created_at,
            'customer_id' => $draft->customer_id,
            'customer_name' => $draft->customer ? $draft->customer->name : null,
            'total' => $draft->total,
            'discount' => $draft->discount,
            'items_count' => $draft->items ? $draft->items->count() : 0,
            'items' => $draft->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->inventoryItem ? $item->inventoryItem->name : '',
                    'quantity' => $item->quantity,
                    'price' => $item->selling_price,
                    'total' => $item->line_total,
                ];
            }),
        ]);
    }

    // POST /api/draft-invoices - Create new draft invoice
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validate the request data with correct table names
            $validated = $request->validate([
                'customer_id' => 'nullable',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'user_id' => 'required|exists:users,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:inventory_items,id',
                'items.*.quantity' => 'required|numeric|min:0.1',
                'items.*.selling_price' => 'required|numeric|min:0',
                'items.*.line_total' => 'required|numeric|min:0',
            ]);

            // Custom validation for customer_id
            if (!empty($validated['customer_id'])) {
                $customerExists = Customer::where('customer_id', $validated['customer_id'])->exists();
                if (!$customerExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected customer does not exist'
                    ], 422);
                }
            }

            // Create draft invoice
            $draftInvoice = DraftInvoice::create([
                'customer_id' => $validated['customer_id'],
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'],
                'total' => $validated['total'],
                'user_id' => $validated['user_id'],
                'status' => 'draft'
            ]);

            // Create draft invoice items with cost_price
            foreach ($validated['items'] as $item) {
                // Get the product to fetch cost price
                $product = InventoryItem::find($item['product_id']);
                $costPrice = $product ? $product->cost : 0;

                DraftInvoiceItem::create([
                    'draft_invoice_id' => $draftInvoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $costPrice, // Add cost_price
                    'selling_price' => $item['selling_price'],
                    'line_total' => $item['line_total'],
                ]);
            }

            DB::commit();

            // Load relationships for response
            $draftInvoice->load(['items.inventoryItem', 'customer']);

            // Prepare items summary for response
            $itemsSummary = $draftInvoice->items->map(function($item) {
                return [
                    'name' => $item->inventoryItem->name ?? 'Unknown Product',
                    'quantity' => $item->quantity,
                    'price' => $item->selling_price,
                    'total' => $item->line_total,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Draft invoice saved successfully',
                'invoice' => [
                    'id' => $draftInvoice->id,
                    'created_at' => $draftInvoice->created_at->toDateTimeString(),
                    'customer_id' => $draftInvoice->customer_id,
                    'customer_name' => $draftInvoice->customer ? $draftInvoice->customer->name : null,
                    'subtotal' => $draftInvoice->subtotal,
                    'discount' => $draftInvoice->discount,
                    'total' => $draftInvoice->total,
                    'items_count' => $draftInvoice->items->count(),
                    'items_summary' => $itemsSummary
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Draft invoice save error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processPayment(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $draftInvoice = DraftInvoice::with(['items', 'customer'])->findOrFail($id);
            
            // Validate payment data
            $validated = $request->validate([
                'method' => 'required|in:cash,card,cheque,credit',
                'amount' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
                'amount_received' => 'required_if:method,cash|numeric|min:0',
                'balance' => 'nullable|numeric',
                'reference' => 'required_if:method,card|string|nullable',
                'bank' => 'nullable|string',
                'cheque_no' => 'required_if:method,cheque|string|nullable',
                'customer_id' => 'required_if:method,credit|string|nullable'
            ]);

            // Check if draft is already paid
            if ($draftInvoice->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'This draft invoice is already paid'
                ], 422);
            }

            $userId = auth()->id() ?? 1;

            // Prepare payment data
            $paymentData = [
                'draft_invoice_id' => $draftInvoice->id,
                'order_total' => $draftInvoice->total,
                'payment_type' => $validated['method'],
                'pay_amount' => $validated['amount'],
                'reference' => $validated['reference'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ];

            // Add method-specific data
            if ($validated['method'] === 'cash') {
                $paymentData['cash_received'] = $validated['amount_received'];
                $paymentData['cash_balance'] = $validated['balance'] ?? 0;
            } elseif ($validated['method'] === 'cheque') {
                $paymentData['cheque_no'] = $validated['cheque_no'];
            }

            // Create payment record
            $payment = DraftBillPayment::create($paymentData);

            $currentBalance = 0;
            $newBalance = 0;

            // Update customer balance if credit payment
            if ($validated['method'] === 'credit' && !empty($validated['customer_id'])) {
                $customer = Customer::where('customer_id', $validated['customer_id'])->first();
                if ($customer) {
                    $currentBalance = $customer->remaining_balance;
                    $customer->remaining_balance += $validated['amount'];
                    $newBalance = $customer->remaining_balance;
                    $customer->save();
                }
            }

            // after creating DraftBillPayment
            $paymentRecord = Payment::create([
                'draft_bill_id' => $draftInvoice->id,
                'payment_method' => $validated['method'],
                'amount' => $validated['amount'],
                'amount_received' => $validated['method'] === 'cash' ? $validated['amount_received'] : null,
                'balance' => $validated['method'] === 'cash' ? $validated['balance'] : null,
                'reference_number' => $validated['method'] === 'card' ? $validated['reference'] : null,
                'cheque_number' => $validated['method'] === 'cheque' ? $validated['cheque_no'] : null,
                'bank' => $validated['bank'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'payment_date' => Carbon::now(),
                'status' => 'completed',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Update draft invoice status
            $draftInvoice->status = 'paid';
            $draftInvoice->save();

            DB::commit();

            // Generate receipt data with credit balance information
            $receiptData = $this->generateReceiptData($draftInvoice, $payment, null, [
                'current_balance' => $currentBalance,
                'new_balance' => $newBalance
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment_id' => $payment->id,
                'receipt_data' => $receiptData
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Draft payment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // GET /api/draft-invoices/{id}/receipt - Get receipt for paid draft
    public function getReceipt($id)
    {
        $draftInvoice = DraftInvoice::with(['items.inventoryItem', 'customer', 'payments'])->findOrFail($id);
        
        if ($draftInvoice->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is not paid yet'
            ], 422);
        }

        $payment = $draftInvoice->payments->first();
        $receiptData = $this->generateReceiptData($draftInvoice, $payment);

        return response()->json([
            'success' => true,
            'receipt_data' => $receiptData
        ]);
    }

    private function convertDraftToSale($draftInvoice, $payment)
    {
        // Optional: Convert draft to permanent sale record
        // You can modify this based on your business logic
        
        $sale = Sale::create([
            'customer_id' => $draftInvoice->customer_id,
            'total_amount' => $draftInvoice->total,
            'discount' => $draftInvoice->discount,
            'tax' => 0, // Adjust as needed
            'grand_total' => $draftInvoice->total,
            'payment_status' => 'paid',
            'sale_date' => Carbon::now(),
            'created_by' => auth()->id() ?? 1,
        ]);

        // Create sale items
        foreach ($draftInvoice->items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->selling_price,
                'total_price' => $item->line_total,
            ]);
        }

        // Create payment record in payments table
        Payment::create([
            'sale_id' => $sale->id,
            'amount' => $payment->pay_amount,
            'payment_method' => $payment->payment_type,
            'payment_date' => Carbon::now(),
            'reference' => $payment->reference,
            'status' => 'completed',
        ]);

        return $sale;
    }

    private function generateReceiptData($draftInvoice, $payment, $sale = null, $creditData = [])
    {
        // Calculate profit for each item
        $itemsWithProfit = $draftInvoice->items->map(function($item) {
            $costPrice = $item->cost_price ?? 0;
            $sellingPrice = $item->selling_price ?? 0;
            $quantity = $item->quantity ?? 0;
            $lineTotal = $item->line_total ?? 0;
            $profitPerItem = $sellingPrice - $costPrice;
            $totalProfit = $profitPerItem * $quantity;

            return [
                'name' => $item->inventoryItem->name ?? 'Unknown Product',
                'quantity' => $quantity,
                'price' => $sellingPrice,
                'total' => $lineTotal,
                'market_price' => $item->inventoryItem->market_price ?? $sellingPrice, // Use market price if available
                'cost_price' => $costPrice,
                'profit_per_item' => $profitPerItem,
                'total_profit' => $totalProfit
            ];
        });

        // Calculate total profit
        $totalProfit = $itemsWithProfit->sum('total_profit');

        return [
            'receipt_number' => 'RCPT-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'invoice_number' => 'DRAFT-' . $draftInvoice->id,
            'sale_number' => $sale ? 'SALE-' . $sale->id : null,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
            'customer' => [
                'id' => $draftInvoice->customer_id,
                'name' => $draftInvoice->customer ? $draftInvoice->customer->name : 'Walk-in Customer',
                'phone' => $draftInvoice->customer ? $draftInvoice->customer->phone_1 : 'N/A',
            ],
            'items' => $itemsWithProfit,
            'totals' => [
                'subtotal' => $draftInvoice->subtotal,
                'discount' => $draftInvoice->discount,
                'total' => $draftInvoice->total,
                'total_profit' => $totalProfit
            ],
            'payment' => [
                'method' => $payment->payment_type,
                'amount' => $payment->pay_amount,
                'cash_received' => $payment->cash_received,
                'cash_balance' => $payment->cash_balance,
                'reference' => $payment->reference,
                'bank' => $payment->bank,
                'cheque_no' => $payment->cheque_no,
                'current_balance' => $creditData['current_balance'] ?? 0,
                'new_balance' => $creditData['new_balance'] ?? 0,
            ]
        ];
    }
}