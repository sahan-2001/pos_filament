<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    // Search by term: name, phone_1, OR customer_id
    public function search(Request $request)
    {
        $term = $request->input('term');

        $customers = Customer::query()
            ->when($term, function ($query, $term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('phone_1', 'like', "%{$term}%");

                // if term is numeric, also search exact customer_id
                if (is_numeric($term)) {
                    $query->orWhere('customer_id', $term);
                }
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['customer_id as id', 'name', 'phone_1', 'remaining_balance']);

        return response()->json($customers);
    }

    // Get customer by id
    public function show($id)
    {
        $customer = Customer::select('customer_id as id', 'name', 'phone_1', 'remaining_balance')
            ->where('customer_id', $id)
            ->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer);
    }
}
