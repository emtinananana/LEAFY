<?php

namespace App\Http\Controllers\customer\cart;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ShoppingCart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class shoppingCartController extends Controller
{
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $shoppingCart = $customer->shoppingCart;
    
        if (!$customer->shoppingCart) {
            return response()->json([
                 'message' => 'The shopping cart is empty.',
            ], 404);
         }
    
        $cartItems = $customer->shoppingCart->cartItems()->with('product')->get();
    
        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'The shopping cart is empty.',
            ], 404);
        }
    
        $shoppingCart = $shoppingCart->load(['cartItems.product' => function ($query) {
            $query->select('id', 'name', 'price', 'product_type');
        }, ]);
        return response()->json(['shoppingCart' => $shoppingCart]);
    
    }
    


    public function addToCart(Request $request, $customerId, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'is_gift' ,
            
            'pot_type' => [
                Rule::requiredIf(function () use ($productId) {
                    $productType = Product::findOrFail($productId)->product_type;
                    return $productType === 'plant';
                }),
            ],
        ]);

        $customer = Customer::findOrFail($customerId);
        $shoppingCart = $customer->shoppingCart;

     $product = Product::findOrFail($productId);
     $cartItem = $shoppingCart->cartItems()->create([
     'quantity' => $request->input('quantity'),
     'is_gift' => $request->input('is_gift', false),
     'pot_type' => $request->input('pot_type', null),
     'product_id' => $productId,
]);
        
return response()->json([
    'success' => true,
    'message' => 'Product added to cart successfully',
    'shoppingCart' => $shoppingCart->load(['cartItems.product' => function ($query) {
        $query->select('id', 'name', 'price', 'product_type');
    }, ])
]);

    }

    public function removeFromCart($customerId, $cartItemId)
{
    
    $customer = Customer::findOrFail($customerId);

    $shoppingCart = $customer->shoppingCart;

    $cartItem = $shoppingCart->cartItems()->findOrFail($cartItemId);

    if (!$cartItem) {
        return response()->json(['error' => 'Cart item not found'], 404);
    }

    $cartItem->delete();

    return response()->json(['message' => 'Cart item removed from cart successfully']);
}

    

}
