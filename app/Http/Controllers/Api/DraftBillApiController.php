<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DraftInvoice;
use App\Models\DraftInvoiceItem;
use App\Models\Customer;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}