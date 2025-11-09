<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryItem;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\EmailService;

class OrderApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Order store request:', $request->all());

            // Try authenticated user first
            $user = auth()->user();

            // If not authenticated, use user_id from the request
            if (!$user && $request->has('user_id')) {
                $user = \App\Models\User::find($request->input('user_id'));
            }

            // If still no user, try to get from session or use default
            if (!$user) {
                $user = Auth::user();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required.',
                ], 401);
            }

            // ✅ Enhanced validation for fractional quantities
            $validator = Validator::make($request->all(), [
                'customer_id' => 'nullable|exists:customers,customer_id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:inventory_items,id',
                'items.*.quantity' => 'required|numeric|min:0.01', // Allow fractional quantities
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.original_price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'status' => 'sometimes|in:draft,completed',
                'payment.method' => 'required_if:status,completed|in:cash,card,cheque,credit',
                'payment.amount_received' => 'required_if:payment.method,cash|numeric|min:0',
                'payment.reference' => 'nullable|string',
                'payment.cheque_no' => 'nullable|string',
                'payment.bank' => 'nullable|string',
                'payment.remarks' => 'nullable|string',
                'payment.customer_id' => 'nullable|exists:customers,customer_id',
                'payment.new_balance' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            DB::beginTransaction();

            $status = $validated['status'] ?? 'completed';
            $isDraft = $status === 'draft';

            // ✅ Create order - let the model handle order_id generation
            $orderData = [
                'user_id' => $user->id,
                'customer_id' => $validated['customer_id'] ?? null,
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'],
                'total' => $validated['total'],
                'status' => $status,
                'order_date' => now(),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            $order = Order::create($orderData);

            Log::info('Order created:', ['order_id' => $order->id, 'order_data' => $orderData]);

            foreach ($validated['items'] as $item) {
                $inventoryItem = InventoryItem::find($item['product_id']);
                if (!$inventoryItem) {
                    throw new \Exception("Inventory item not found (ID {$item['product_id']}).");
                }

                // Format quantity to 2 decimal places for consistency
                $quantity = number_format($item['quantity'], 2, '.', '');
                
                // Only check stock and decrement for completed orders, not drafts
                if (!$isDraft) {
                    // Check if sufficient stock exists (with decimal precision)
                    if ($inventoryItem->available_quantity < $quantity) {
                        throw new \Exception("Insufficient stock for {$inventoryItem->name}. Available: {$inventoryItem->available_quantity}, Requested: {$quantity}");
                    }
                    
                    // Decrement with decimal precision using the new method
                    $inventoryItem->decrementQuantity($quantity, $user->id);
                    
                    Log::info('Inventory updated:', [
                        'product_id' => $item['product_id'],
                        'product_name' => $inventoryItem->name,
                        'quantity_deducted' => $quantity,
                        'remaining_stock' => $inventoryItem->available_quantity,
                        'updated_by' => $user->id
                    ]);
                }

                // ✅ Create order item with regular prices from inventory
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $item['unit_price'], // Actual selling price used in order
                    'original_price' => $item['original_price'], // Original price before any changes
                    'line_total' => $quantity * $item['unit_price'],
                    'regular_market_price' => $inventoryItem->market_price, // From inventory item
                    'regular_selling_price' => $inventoryItem->selling_price, // From inventory item
                    'cost' => $inventoryItem->cost, // From inventory item
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);

                Log::info('Order item created with regular prices:', [
                    'product_id' => $item['product_id'],
                    'regular_market_price' => $inventoryItem->market_price,
                    'regular_selling_price' => $inventoryItem->selling_price,
                    'cost' => $inventoryItem->cost,
                    'unit_price_used' => $item['unit_price']
                ]);
            }

            Log::info('Order items created:', ['order_id' => $order->id, 'items_count' => count($validated['items'])]);

            // Only create payment for completed orders, not drafts
            if (!$isDraft && isset($validated['payment'])) {
                $paymentData = $validated['payment'];
                
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $validated['total'],
                    'payment_method' => $paymentData['method'],
                    'payment_status' => 'completed',
                    'payment_date' => now(),
                    'amount_received' => $paymentData['amount_received'] ?? $validated['total'],
                    'balance' => ($paymentData['amount_received'] ?? $validated['total']) - $validated['total'],
                    'reference_number' => $paymentData['reference'] ?? null,
                    'cheque_number' => $paymentData['cheque_no'] ?? null,
                    'bank_name' => $paymentData['bank'] ?? null,
                    'remarks' => $paymentData['remarks'] ?? null,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);

                Log::info('Payment created:', ['order_id' => $order->id, 'method' => $paymentData['method']]);

                // Update customer balance for credit payments
                if ($paymentData['method'] === 'credit' && isset($paymentData['customer_id'])) {
                    $customer = Customer::find($paymentData['customer_id']);
                    if ($customer) {
                        $newBalance = $paymentData['new_balance'] ?? ($customer->remaining_balance + $validated['total']);
                        $customer->update(['remaining_balance' => $newBalance]);
                        Log::info('Customer balance updated:', [
                            'customer_id' => $customer->id,
                            'new_balance' => $newBalance
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Draft order saved successfully' : 'Order created successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_id, 
                'user_id' => $user->id,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed: '.$e->getMessage(),
            ], 500);
        }
    }

    // Add method to save draft specifically
    public function storeDraft(Request $request)
    {
        $request->merge(['status' => 'draft']);
        return $this->store($request);
    }

    // Add method to get draft orders
    public function getDrafts(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user && $request->has('user_id')) {
                $user = \App\Models\User::find($request->input('user_id'));
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required.',
                ], 401);
            }

            $drafts = Order::with(['customer', 'items.product'])
                ->where('status', 'draft')
                ->where('created_by', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'drafts' => $drafts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch drafts: '.$e->getMessage(),
            ], 500);
        }
    }

    // Convert draft to Order
    public function convertDraftToOrder(Request $request, $draftId)
    {
        try {
            DB::beginTransaction();

            $draft = Order::with('items')->findOrFail($draftId);
            
            if ($draft->status !== 'draft') {
                throw new \Exception('This order is not a draft.');
            }

            // Check stock availability before converting
            foreach ($draft->items as $item) {
                $inventoryItem = InventoryItem::find($item->product_id);
                if ($inventoryItem) {
                    if ($inventoryItem->available_quantity < $item->quantity) {
                        throw new \Exception("Insufficient stock for {$inventoryItem->name}. Available: {$inventoryItem->available_quantity}, Requested: {$item->quantity}");
                    }
                }
            }

            // Update status to completed - order_id remains the same
            $draft->update([
                'status' => 'completed',
                // Remove the order_number generation as we're using order_id
            ]);

            // Process inventory deduction with decimal precision
            foreach ($draft->items as $item) {
                $inventoryItem = InventoryItem::find($item->product_id);
                if ($inventoryItem) {
                    $inventoryItem->decrementQuantity($item->quantity, $draft->created_by);
                    
                    Log::info('Inventory updated for draft conversion:', [
                        'product_id' => $item->product_id,
                        'quantity_deducted' => $item->quantity,
                        'remaining_stock' => $inventoryItem->available_quantity
                    ]);
                }
            }

            // Create payment record if payment data is provided
            if ($request->has('payment')) {
                $paymentData = $request->validate([
                    'payment.method' => 'required|in:cash,card,cheque,credit',
                    'payment.amount_received' => 'required_if:payment.method,cash|numeric|min:0',
                    'payment.reference' => 'nullable|string',
                    'payment.cheque_no' => 'nullable|string',
                    'payment.bank' => 'nullable|string',
                    'payment.remarks' => 'nullable|string',
                ])['payment'];

                Payment::create([
                    'order_id' => $draft->id,
                    'amount' => $draft->total,
                    'payment_method' => $paymentData['method'],
                    'payment_status' => 'completed',
                    'payment_date' => now(),
                    'amount_received' => $paymentData['amount_received'] ?? $draft->total,
                    'balance' => ($paymentData['amount_received'] ?? $draft->total) - $draft->total,
                    'reference_number' => $paymentData['reference'] ?? null,
                    'cheque_number' => $paymentData['cheque_no'] ?? null,
                    'bank_name' => $paymentData['bank'] ?? null,
                    'remarks' => $paymentData['remarks'] ?? null,
                    'created_by' => $draft->created_by,
                    'updated_by' => $draft->created_by,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Draft converted to order successfully',
                'order_id' => $draft->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed: '.$e->getMessage(),
            ], 500);
        }
    }

    private function generateOrderNumber()
    {
        $prefix = 'INV'; 
        $date = now()->format('Ymd');
        
        $lastOrder = Order::whereDate('created_at', today())
                        ->latest()
                        ->first();
        
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getReceipt($id)
{
    try {
        $order = Order::with(['customer', 'items.inventoryItem', 'payment'])
                    ->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $order,
            'receipt_data' => [
                'order_number' => $order->order_id,
                'order_date' => $order->order_date,
                'customer_name' => $order->customer ? $order->customer->name : 'Walk-in Customer',
                'customer_phone' => $order->customer ? $order->customer->phone_1 : 'N/A',
                'items' => $order->items->map(function($item) {
                    $regularMarketPrice = $item->regular_market_price ?? $item->inventoryItem?->market_price ?? 0;
                    $originalPrice      = $item->original_price ?? $item->unit_price ?? $item->inventoryItem?->cost ?? 0;

                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->inventoryItem?->name ?? $item->product?->name ?? 'Item',
                        'quantity' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'original_price' => (float) $originalPrice,
                        'regular_market_price' => (float) $regularMarketPrice,
                        'regular_selling_price' => (float) ($item->regular_selling_price ?? $item->inventoryItem?->selling_price ?? 0),
                        'cost' => (float) ($item->cost ?? $item->inventoryItem?->cost ?? 0),
                        'line_total' => (float) $item->line_total,
                    ];
                }),
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'total' => (float) $order->total,
                'payment_method' => $order->payment ? $order->payment->payment_method : 'N/A',
                'payment_details' => $order->payment ? [
                    'amount_received' => (float) $order->payment->amount_received,
                    'balance' => (float) $order->payment->balance,
                    'reference_number' => $order->payment->reference_number,
                    'bank_name' => $order->payment->bank_name,
                    'cheque_number' => $order->payment->cheque_number,
                    'remarks' => $order->payment->remarks
                ] : null
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch receipt: ' . $e->getMessage()
        ], 500);
    }
}

private function handlePostPaymentActions(Order $order, $paymentData)
{
    $emailService = new EmailService();
    $emailResult = null;
    $shouldPrint = true;

    // Check if customer has email and send e-bill
    if ($order->customer && $order->customer->email) {
        $emailResult = $emailService->sendEbill($order);
        $shouldPrint = false; // Don't auto-print for customers with email
    }

    return [
        'email_sent' => $emailResult ? $emailResult['success'] : false,
        'email_message' => $emailResult ? $emailResult['message'] : null,
        'should_print' => $shouldPrint,
        'customer_has_email' => $order->customer && $order->customer->email
    ];
}


}