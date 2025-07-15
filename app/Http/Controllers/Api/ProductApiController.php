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

        $query = InventoryItem::query()
            ->where('available_quantity', '>', 0);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%");
            });
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
                'image' => $item->image ? asset("storage/{$item->image}") : null,
            ];
        });

        return response()->json($items);
    }
}