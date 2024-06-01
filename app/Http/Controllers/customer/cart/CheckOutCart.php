<?php

namespace App\Http\Controllers\customer\cart;
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
    public function checkoutCart(Request $request)
    {
        $giftDetails = [];
        $customer = auth()->guard('customer-api')->user();
        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    
        $cartItems = $customer->shoppingCart->cartItems;

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
           
            foreach ($cartItems as $cartItem) {
             
                $quantity = $cartItem->quantity;
                $productId = $cartItem->product_id; 
                   
                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $order->id; 
                    $orderProduct->product_id = $productId;
                    $orderProduct->quantity = $quantity; 
                    $orderProduct->save();
                  
                    if ($cartItem->is_gift ) {
                        $giftData = $request->input()[$cartItem->id];
                        $recipientName = $giftData['recipient_name'];
                        $recipientPhone = $giftData['recipient_phone'];
                        $recipientAddress = $giftData['recipient_address'];
                        $note = $giftData['note'];
                
                        $giftDetail = new GiftDetail();
                        $giftDetail->order_product_id = $orderProduct->id;
                        $giftDetail->order_id = $order->id;
                        $giftDetail->recipient_name = $recipientName;
                        $giftDetail->recipient_phone = $recipientPhone;
                        $giftDetail->recipient_address = $recipientAddress;
                        $giftDetail->note = $note;
                        $giftDetail->save();
                        $giftDetails[] = $giftDetail; 
                        }
                    
    
                $customer->history()->create(['order_id' => $order->id]);
            }

            $customer->shoppingCart->cartItems()->delete();

            DB::commit();

            return response()->json(['message' => 'Order placed successfully', 'order' => $order, 'orderProducts'=> $orderProduct, 'giftdetails' => $giftDetails ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'An error occurred while placing the order', 'error' => $e->getMessage()], 500);
        }
    }
}
