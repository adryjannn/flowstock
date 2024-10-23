<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BestSellingProductsController extends Controller
{
    public function index(Request $request)
    {
        $bestSellers = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product') // Ładowanie produktów
            ->take(10) // Top 10
            ->get();

        return response()->json($bestSellers);
    }
}
