<?php

namespace App\Http\Controllers\customer\history;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use App\Models\Order;
use App\Models\History;
use Carbon\Carbon;

class HistoryController extends Controller
{

    public function showHistory(Request $request)
    {
        $customer = auth()->guard('customer-api')->user();

        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $orders = Order::with(['orderProducts.product', 'orderProducts.giftDetails'])
            ->where('customer_id', $customer->id)
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json(['orders' => $orders]);
    }

    public function cancelOrder(Request $request, Order $order)
    {
        $customer = auth()->guard('customer-api')->user();

        if (!$customer || $order->customer_id !== $customer->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Only pending orders can be canceled'], 400);
        }

        $order->delete();

        return response()->json(['message' => 'Order canceled successfully']);
    }
}

