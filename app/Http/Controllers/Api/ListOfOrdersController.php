<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ListOfOrdersController extends Controller
{
    /**
     * GET /api/orders - Get orders with filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('ListOfOrdersController: index method called');
            
            $status = $request->get('status');
            $date = $request->get('date');
            $search = $request->get('search');

            $query = Order::with(['customer', 'items', 'payment'])
                ->select([
                    'id',
                    'order_id',
                    'customer_id', 
                    'subtotal',
                    'discount',
                    'total',
                    'status',
                    'order_date',
                    'created_by'
                ]);

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            // Filter by date
            if ($date) {
                $query->whereDate('order_date', $date);
            }

            // Search by order_id or customer name
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($customerQuery) use ($search) {
                          $customerQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $orders = $query->orderByDesc('order_date')->get()->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'order_date' => $order->order_date,
                    'customer_id' => $order->customer_id,
                    'customer_name' => $order->customer ? $order->customer->name : null,
                    'customer_phone' => $order->customer ? $order->customer->phone_1 : null,
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount,
                    'total' => $order->total,
                    'status' => $order->status,
                    'items_count' => $order->items ? $order->items->count() : 0,
                    'payment_status' => $order->payment ? $order->payment->status : null,
                    'payment_method' => $order->payment ? $order->payment->payment_method : null,
                    'payment_date' => $order->payment ? $order->payment->payment_date : null,
                    'created_by_name' => $order->createdBy ? $order->createdBy->name : 'System',
                ];
            });

            Log::info('ListOfOrdersController: Successfully returned ' . count($orders) . ' orders');
            return response()->json($orders);

        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to fetch orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id} - Get single order details
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::with([
                'customer', 
                'items.inventoryItem',
                'payment',
                'createdBy'
            ])->find($id);

            if (!$order) {
                return response()->json([
                    'error' => 'Order not found'
                ], 404);
            }

            $orderData = [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'order_date' => $order->order_date,
                'customer_id' => $order->customer_id,
                'customer_name' => $order->customer ? $order->customer->name : 'Walk-in Customer',
                'customer_phone' => $order->customer ? $order->customer->phone_1 : 'N/A',
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'total' => $order->total,
                'status' => $order->status,
                'created_by_name' => $order->createdBy ? $order->createdBy->name : 'System',
                'payment_status' => $order->payment ? $order->payment->status : null,
                'payment_method' => $order->payment ? $order->payment->payment_method : null,
                'payment_date' => $order->payment ? $order->payment->payment_date : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->inventoryItem ? $item->inventoryItem->name : 'Unknown Product',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'cost' => $item->cost ?? 0,
                        'line_total' => $item->line_total,
                        'regular_market_price' => $item->regular_market_price ?? $item->unit_price,
                        'regular_selling_price' => $item->regular_selling_price ?? $item->unit_price,
                    ];
                })
            ];

            return response()->json($orderData);

        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch order details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}/payments - Get payment details for order
     */
    public function getPayments($id): JsonResponse
    {
        try {
            $payments = Payment::where('order_id', $id)->get();

            $paymentData = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'payment_date' => $payment->payment_date,
                    'reference_number' => $payment->reference_number,
                    'cheque_number' => $payment->cheque_number,
                    'bank' => $payment->bank,
                    'remarks' => $payment->remarks,
                    'amount_received' => $payment->amount_received,
                    'balance' => $payment->balance,
                ];
            });

            return response()->json($paymentData);

        } catch (\Exception $e) {
            Log::error('Error fetching payment details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch payment details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}/receipt - Get receipt data for order
     */
    public function getReceipt($id): JsonResponse
    {
        try {
            $order = Order::with([
                'customer', 
                'items.inventoryItem',
                'payment'
            ])->find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Calculate profit for each item
            $itemsWithProfit = $order->items->map(function ($item) {
                $costPrice = $item->cost ?? 0;
                $sellingPrice = $item->unit_price ?? 0;
                $quantity = $item->quantity ?? 0;
                $lineTotal = $item->line_total ?? 0;
                $profitPerItem = $sellingPrice - $costPrice;
                $totalProfit = $profitPerItem * $quantity;

                return [
                    'name' => $item->inventoryItem->name ?? 'Unknown Product',
                    'quantity' => $quantity,
                    'price' => $sellingPrice,
                    'total' => $lineTotal,
                    'market_price' => $item->regular_market_price ?? $sellingPrice,
                    'cost_price' => $costPrice,
                    'profit_per_item' => $profitPerItem,
                    'total_profit' => $totalProfit
                ];
            });

            // Calculate total profit
            $totalProfit = $itemsWithProfit->sum('total_profit');

            $receiptData = [
                'receipt_number' => 'RCPT-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'order_number' => $order->order_id,
                'date' => $order->order_date ? $order->order_date->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'customer' => [
                    'id' => $order->customer_id,
                    'name' => $order->customer ? $order->customer->name : 'Walk-in Customer',
                    'phone' => $order->customer ? $order->customer->phone_1 : 'N/A',
                ],
                'items' => $itemsWithProfit,
                'totals' => [
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount,
                    'total' => $order->total,
                    'total_profit' => $totalProfit
                ],
                'payment' => [
                    'method' => $order->payment ? $order->payment->payment_method : 'cash',
                    'amount' => $order->payment ? $order->payment->amount : $order->total,
                    'cash_received' => $order->payment ? $order->payment->amount_received : null,
                    'cash_balance' => $order->payment ? $order->payment->balance : null,
                    'reference' => $order->payment ? $order->payment->reference_number : null,
                    'bank' => $order->payment ? $order->payment->bank : null,
                    'cheque_no' => $order->payment ? $order->payment->cheque_number : null,
                    'current_balance' => 0,
                    'new_balance' => 0,
                ]
            ];

            return response()->json([
                'success' => true,
                'receipt_data' => $receiptData
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating receipt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/orders/{id}/status - Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,canceled'
            ]);

            $order = Order::find($id);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $oldStatus = $order->status;
            $newStatus = $request->status;

            $order->update([
                'status' => $newStatus,
                'updated_by' => auth()->id() ?? 1
            ]);

            // Log the status change
            Log::info("Order {$order->order_id} status changed from {$oldStatus} to {$newStatus}");

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'status' => $order->status
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }
}