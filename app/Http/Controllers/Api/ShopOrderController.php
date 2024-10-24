<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopOrderController extends Controller
{
    public function store(Request $request)
    {
        // Walidacja danych zamówienia
        $validatedData = $request->validate([
            'id_shop_order' => 'required|string|unique:shop_orders,id_shop_order',
            'order_reference' => 'required|string',
            'payment_type' => 'required|string',
            'carrier' => 'required|string',
            'total_paid' => 'required|numeric',
            'total_shipping' => 'required|numeric',
            'products' => 'required|array',
            'products.*.product_code' => 'required|string',
            'products.*.product_name' => 'required|string',
            'products.*.product_price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {

            DB::beginTransaction();


            $shopOrder = ShopOrder::create([
                'id_shop_order' => $validatedData['id_shop_order'],
                'order_reference' => $validatedData['order_reference'],
                'payment_type' => $validatedData['payment_type'],
                'carrier' => $validatedData['carrier'],
                'order_state' => 'Nowe',
                'total_paid' => $validatedData['total_paid'],
                'total_shipping' => $validatedData['total_shipping'],
            ]);


            foreach ($validatedData['products'] as $productData) {
                ShopOrderProduct::create([
                    'id_order' => $shopOrder->id,
                    'id_shop_order' => $shopOrder->id_shop_order,
                    'product_code' => $productData['product_code'],
                    'product_name' => $productData['product_name'],
                    'product_price' => $productData['product_price'],
                    'quantity' => $productData['quantity'],
                ]);
            }


            DB::commit();

            return response()->json(['message' => 'Order created successfully', 'order_id' => $shopOrder->id], 201);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['error' => 'Order creation failed', 'message' => $e->getMessage()], 500);
        }
    }


    public function getOrders(Request $request)
    {

        $defaultSortBy = 'created_at';
        $defaultSortOrder = 'desc';
        $defaultPerPage = 10;

        $query = ShopOrder::query();

        $filters = [
            'id_shop_order',
            'order_reference',
            'payment_type',
            'carrier',
            'order_state',
            'total_paid',
            'total_shipping',
        ];

        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                $query->where($filter, $request->query($filter));
            }
        }

        if ($request->has('product_code')) {
            $query->whereHas('products', function ($q) use ($request) {
                $q->where('product_code', $request->query('product_code'));
            });
        }

        $orders = $query->orderBy(
            $request->query('sort_by', $defaultSortBy),
            $request->query('sort_order', $defaultSortOrder)
        )
            ->paginate($request->query('per_page', $defaultPerPage));

        return response()->json([
            'current_page' => $orders->currentPage(),
            'total_orders' => $orders->total(),
            'per_page' => $orders->perPage(),
            'total_pages' => $orders->lastPage(),
            'orders' => $orders->items(),
        ], 200);
    }

    public function updateOrderStatus(Request $request)
    {

        $request->validate([
            'id' => 'required|integer|exists:shop_orders,id',
            'order_state' => 'required|string',
        ]);


        $order = ShopOrder::find($request->input('id'));


        $order->order_state = $request->input('order_state');
        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully',
            'order_id' => $order->id,
            'order_state' => $order->order_state
        ], 200);
    }
}
