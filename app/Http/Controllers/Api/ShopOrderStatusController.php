<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopOrderStatus;
use Illuminate\Http\JsonResponse;

class ShopOrderStatusController extends Controller
{
    public function index(): JsonResponse
    {

        $orderStatuses = ShopOrderStatus::select('shop_order_status_id', 'name')->get();

        return response()->json($orderStatuses);
    }

    public function show($id): JsonResponse
    {
        $orderStatus = ShopOrderStatus::select('shop_order_status_id', 'name')
            ->find($id);

        if (!$orderStatus) {
            return response()->json(['error' => 'Order status not found'], 404);
        }

        return response()->json($orderStatus);
    }
}
