<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->get('term');
        $barcode = $request->get('barcode');
        $itemCode = $request->get('item_code');

        $query = InventoryItem::query()
            ->where('available_quantity', '>', 0);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                ->orWhere('item_code', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        // Add direct search by barcode (exact match)
        if ($barcode) {
            $query->where('barcode', $barcode);
        }

        // Add direct search by item_code (exact match)
        if ($itemCode) {
            $query->where('item_code', $itemCode);
        }

        $items = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'barcode' => $item->barcode,
                'name' => $item->name,
                'market_price' => $item->market_price ?? 0,
                'selling_price' => $item->selling_price ?? 0,
                'available_quantity' => $item->available_quantity,
                'category' => $item->category,
                'image' => $item->image ? asset("storage/{$item->image}") : null,
            ];
        });

        return response()->json($items);
    }
}