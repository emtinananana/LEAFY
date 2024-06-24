<?php
namespace App\Http\Controllers\admin\orders;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order ; 
use App\Models\Customer ; 
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
 public function showOrders()
 {
    $orders = Order::with('customer', 'orderProducts.product', 'orderProducts.giftDetails')->get();
 if ($orders->isEmpty()) {
    return response()->json(['message' => 'No orders found.'], 404);
}
return response()->json($orders, 200);
 }

 public function destroy($id)
 {
     try {
         DB::transaction(function () use ($id) {
             $order = Order::with(['orderProducts.giftDetails'])->findOrFail($id);

             $order->orderProducts->each(function ($orderProduct) {
                 $orderProduct->giftDetails()->delete();
             });

             $order->orderProducts()->delete();
             $order->delete();
         });

         return response()->json(['message' => 'Order deleted successfully.'], 200);
     } catch (\Exception $e) {
         return response()->json(['message' => 'Error deleting order.', 'error' => $e->getMessage()], 500);
     }
 }

 public function show($id)
    {
        $order = order::findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = order::findOrFail($id);
        
        $request->validate([
            'status' => [
                'required',
                Rule::in(['pending', 'shipped', 'canceled', 'delivered']),
            ],
            'delivery_date' => 'nullable|date',
        ]);
    
       
        $order->update([
            'status' => $request->input('status'),
            'delivery_date' => $request->input('delivery_date'),
        ]);

        return response()->json($order);
    }
    public function search(string $name)
{
    $orders = Order::with('customer')
                   ->whereHas('customer', function ($query) use ($name) {
                       $query->where('name', 'like', '%' . $name . '%');
                   })
                   ->get();

    return response()->json($orders);
}
    
}
