<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->get('search');
        $categoryId = $request->get('category_id');
        $barcode = $request->get('barcode');
        $itemCode = $request->get('item_code');

        $query = InventoryItem::with('categoryRelation')
            ->where('available_quantity', '>', 0);

        // Search by term
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                ->orWhere('item_code', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        // Filter by category_id
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Direct searches
        if ($barcode) {
            $query->where('barcode', $barcode);
        }
        if ($itemCode) {
            $query->where('item_code', $itemCode);
        }

        $items = $query->get()->map(function ($item) {
            // Enhanced image URL handling
            $imageUrl = $this->getImageUrl($item->image);

            return [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'barcode' => $item->barcode,
                'name' => $item->name,
                'market_price' => $item->market_price ?? 0,
                'selling_price' => $item->selling_price ?? 0,
                'cost' => $item->cost ?? 0,
                'available_quantity' => $item->available_quantity,
                'category' => $item->categoryRelation ? $item->categoryRelation->name : $item->category,
                'category_id' => $item->category_id,
                'image' => $imageUrl,
            ];
        });

        return response()->json($items);
    }

    /**
     * Get the proper image URL for the product
     */
    private function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Remove any leading slashes
        $imagePath = ltrim($imagePath, '/\\');

        // Check if file exists in storage
        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->url($imagePath);
        }

        // Check if it's in the default storage location
        $defaultPaths = [
            $imagePath,
            'products/' . $imagePath,
            'images/' . $imagePath,
            'inventory/' . $imagePath,
        ];

        foreach ($defaultPaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        }

        // If no image found, return null
        return null;
    }

    public function getCompanyInfo()
    {
        try {
            // Get the first company record
            $company = Company::first();
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company information not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'company' => [
                    'name' => $company->name,
                    'address_line_1' => $company->address_line_1,
                    'address_line_2' => $company->address_line_2,
                    'address_line_3' => $company->address_line_3,
                    'city' => $company->city,
                    'postal_code' => $company->postal_code,
                    'country' => $company->country,
                    'primary_phone' => $company->primary_phone,
                    'secondary_phone' => $company->secondary_phone,
                    'email' => $company->email,
                    'logo' => $company->logo_url, // Use the accessor
                    'formatted_address' => $company->formatted_address, // Use the accessor
                    'special_notes' => $company->special_notes,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching company info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch company information'
            ], 500);
        }
    }

    /**
     * Format company address
     */
    private function getFormattedAddress($company)
    {
        $addressParts = [
            $company->address_line_1,
            $company->address_line_2,
            $company->address_line_3,
            $company->city,
            $company->postal_code,
            $company->country
        ];

        return implode(', ', array_filter($addressParts));
    }
}