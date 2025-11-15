<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ReturnOrder;
use App\Models\ReturnOrderItem;
use App\Models\Refund;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnOrderController extends Controller
{
    /**
     * POST /api/orders/return - Process order return
     */
    public function processReturn(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'return_reason' => 'required|string',
                'refund_amount' => 'required|numeric|min:0',
                'refund_method' => 'required|string',
                'return_items' => 'required|array|min:1',
                'return_items.*.order_item_id' => 'required|exists:order_items,id',
                'return_items.*.quantity' => 'required|integer|min:1',
                'return_items.*.refund_amount' => 'required|numeric|min:0'
            ]);

            $order = Order::with(['items', 'payment'])->find($request->order_id);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Create return order record
            $returnOrder = ReturnOrder::create([
                'order_id' => $order->id,
                'return_number' => 'RET-' . str_pad(ReturnOrder::count() + 1, 6, '0', STR_PAD_LEFT),
                'return_reason' => $request->return_reason,
                'total_refund_amount' => $request->refund_amount,
                'return_date' => now(),
                'status' => 'completed',
                'processed_by' => auth()->id() ?? 1,
                'notes' => $request->notes ?? null
            ]);

            $returnedItems = [];
            $totalItemsReturned = 0;

            // Process each returned item
            foreach ($request->return_items as $returnItem) {
                $orderItem = OrderItem::find($returnItem['order_item_id']);
                
                if (!$orderItem) {
                    throw new \Exception("Order item not found: " . $returnItem['order_item_id']);
                }

                // Validate return quantity doesn't exceed original quantity
                if ($returnItem['quantity'] > $orderItem->quantity) {
                    throw new \Exception("Return quantity exceeds original quantity for item: " . $orderItem->product_name);
                }

                // Create return order item
                $returnOrderItem = ReturnOrderItem::create([
                    'return_order_id' => $returnOrder->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $returnItem['quantity'],
                    'unit_price' => $orderItem->unit_price,
                    'refund_amount' => $returnItem['refund_amount'],
                    'reason' => $request->return_reason
                ]);

                // Update inventory stock
                $inventoryItem = InventoryItem::find($orderItem->product_id);
                if ($inventoryItem) {
                    $inventoryItem->increment('current_stock', $returnItem['quantity']);
                    
                    // Log inventory adjustment
                    Log::info("Inventory updated for product {$inventoryItem->name}: +{$returnItem['quantity']} units (return)");
                }

                $returnedItems[] = $returnOrderItem;
                $totalItemsReturned += $returnItem['quantity'];
            }

            // Create refund record
            $refund = Refund::create([
                'return_order_id' => $returnOrder->id,
                'order_id' => $order->id,
                'amount' => $request->refund_amount,
                'refund_method' => $request->refund_method,
                'status' => 'completed',
                'refund_date' => now(),
                'processed_by' => auth()->id() ?? 1,
                'reference_number' => 'REF-' . str_pad(Refund::count() + 1, 6, '0', STR_PAD_LEFT),
                'notes' => "Refund for return #{$returnOrder->return_number}"
            ]);

            // Update order status if all items are returned
            $totalOriginalItems = $order->items->sum('quantity');
            if ($totalItemsReturned >= $totalOriginalItems) {
                $order->update([
                    'status' => 'returned',
                    'updated_by' => auth()->id() ?? 1
                ]);
            } else {
                $order->update([
                    'status' => 'partially_returned',
                    'updated_by' => auth()->id() ?? 1
                ]);
            }

            DB::commit();

            // Log the return
            Log::info("Return processed for order {$order->order_id}, refund amount: {$request->refund_amount}");

            return response()->json([
                'success' => true,
                'message' => 'Return processed successfully',
                'data' => [
                    'return_order' => $returnOrder,
                    'refund' => $refund,
                    'returned_items' => $returnedItems
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
            Log::error('Error processing return: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}/returns - Get return history for order
     */
    public function getReturnHistory($id): JsonResponse
    {
        try {
            $returns = ReturnOrder::with(['items', 'refund'])
                ->where('order_id', $id)
                ->orderByDesc('return_date')
                ->get();

            $returnData = $returns->map(function ($return) {
                return [
                    'id' => $return->id,
                    'return_number' => $return->return_number,
                    'return_date' => $return->return_date,
                    'return_reason' => $return->return_reason,
                    'total_refund_amount' => $return->total_refund_amount,
                    'status' => $return->status,
                    'items' => $return->items->map(function ($item) {
                        return [
                            'product_name' => $item->orderItem->inventoryItem->name ?? 'Unknown Product',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'refund_amount' => $item->refund_amount
                        ];
                    }),
                    'refund' => $return->refund ? [
                        'amount' => $return->refund->amount,
                        'method' => $return->refund->refund_method,
                        'status' => $return->refund->status,
                        'reference_number' => $return->refund->reference_number
                    ] : null
                ];
            });

            return response()->json($returnData);

        } catch (\Exception $e) {
            Log::error('Error fetching return history: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch return history',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}