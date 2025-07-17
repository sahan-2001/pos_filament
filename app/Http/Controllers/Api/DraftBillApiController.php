<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DraftInvoice;
use App\Models\DraftInvoiceItem;
use Illuminate\Http\Request;

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
}