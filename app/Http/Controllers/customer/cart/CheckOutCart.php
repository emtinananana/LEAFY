<?php
namespace App\Http\Controllers\customer\cart;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\GiftDetail;
use App\Models\CartItem;
use Carbon\Carbon;

class CheckoutCart extends Controller
{
    public function getGiftProducts()
    {
        $customer = Auth::guard('customer-api')->user(); 
        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shoppingCart = $customer->shoppingCart;
        if (!$shoppingCart) {
            return response()->json(['message' => 'No shopping cart found for this customer'], 400);
        }

        $cartItems = $shoppingCart->cartItems()->where('is_gift', true)->with('product')->get();

        return response()->json(['gift_products' => $cartItems]);
    }

    public function checkoutCart(Request $request)
    {
        $customer = auth()->guard('customer-api')->user();
        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shoppingCart = $customer->shoppingCart;
        if (!$shoppingCart) {
            return response()->json(['message' => 'No shopping cart found for this customer'], 400);
        }

        $cartItems = $shoppingCart->cartItems;

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty'], 400);
        }

        $totalAmount = 0;

        DB::beginTransaction();

        try {
            foreach ($cartItems as $cartItem) {
                $totalAmount += $cartItem->product->price * $cartItem->quantity;
            }

            $order = new Order();
            $order->customer_id = $customer->id;
            $order->status = 'pending';
            $order->order_date = Carbon::now();
            $order->total = $totalAmount;
            $order->save();

            $giftDetailsData = $request->input('gift_details', []);

            foreach ($cartItems as $cartItem) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $cartItem->product_id;
                $orderProduct->quantity = $cartItem->quantity;
                $orderProduct->pot_type = $cartItem->pot_type;
                $orderProduct->save();

                if ($cartItem->is_gift) {
                    $giftDetail = collect($giftDetailsData)->firstWhere('product_id', $cartItem->product_id);
                    if ($giftDetail) {
                        $orderProduct->giftDetails()->create([
                            'order_id' => $order->id,
                            'order_product_id' => $orderProduct->id,
                            'recipient_name' => $giftDetail['recipient_name'],
                            'recipient_phone' => $giftDetail['recipient_phone'],
                            'recipient_address' => $giftDetail['recipient_address'],
                            'note' => $giftDetail['note']
                        ]);
                    }
                }
            }

            $shoppingCart->cartItems()->delete();

            DB::commit();

            $order->load('orderProducts.giftDetails');

            return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'An error occurred while placing the order', 'error' => $e->getMessage()], 500);
        }
    }
}
