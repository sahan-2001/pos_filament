<?php

namespace App\Http\Controllers;

use App\Models\DraftInvoice;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DraftInvoiceController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,customer_id',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:inventory_items,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.cost_price' => 'required|numeric|min:0',
                'items.*.selling_price' => 'required|numeric|min:0',
                'items.*.line_total' => 'required|numeric|min:0',
            ]);

            $invoiceData = [
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $validated['customer_id'],
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'],
                'total' => $validated['total'],
                'status' => 'draft',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(), 
            ];

            $draftInvoice = DraftInvoice::create($invoiceData);

            $itemsSummary = [];
            $totalItems = 0;

            foreach ($validated['items'] as $item) {
                $inventoryItem = InventoryItem::findOrFail($item['product_id']);
                
                if ($inventoryItem->track_stock && $inventoryItem->available_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$inventoryItem->name}. Available: {$inventoryItem->available_quantity}");
                }

                $draftInvoice->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'line_total' => $item['line_total'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(), 
                ]);

                if ($inventoryItem->track_stock) {
                    $inventoryItem->decrement('available_quantity', $item['quantity']);
                }

                $itemsSummary[] = [
                    'name' => $inventoryItem->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['selling_price'],
                    'total' => $item['line_total']
                ];
                $totalItems += $item['quantity'];
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => [
                    'id' => $draftInvoice->id,
                    'invoice_number' => $draftInvoice->invoice_number,
                    'customer_id' => $draftInvoice->customer_id,
                    'subtotal' => $draftInvoice->subtotal,
                    'discount' => $draftInvoice->discount,
                    'total' => $draftInvoice->total,
                    'created_at' => $draftInvoice->created_at->format('Y-m-d H:i:s'),
                    'items_count' => $totalItems,
                    'items_summary' => $itemsSummary
                ],
                'message' => 'Draft invoice saved successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function generateInvoiceNumber()
    {
        $prefix = 'DRAFT-';
        $latest = DraftInvoice::latest()->first();
        $number = $latest ? (int) str_replace($prefix, '', $latest->invoice_number) + 1 : 1;
        
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}