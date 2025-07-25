<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Category;

class POSController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return view('pos.index', [
            'user' => $user
        ]);
    }
}
