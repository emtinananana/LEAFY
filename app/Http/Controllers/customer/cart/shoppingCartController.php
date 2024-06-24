<?php 

namespace App\Http\Controllers\Customer\Cart;

use App\Models\Customer;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ShoppingCart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    public function show()
    {
        $customer = Auth::guard('customer-api')->user();

        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shoppingCart = $customer->shoppingCart;

        if (!$shoppingCart) {
            return response()->json(['message' => 'The shopping cart is empty.'], 404);
        }

        $shoppingCart = $shoppingCart->load(['cartItems.product' => function ($query) {
            $query->select('id', 'name', 'price', 'product_type');
        }]);

        return response()->json(['shoppingCart' => $shoppingCart]);
    }

    public function addToCart(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'is_gift' => 'boolean',
            'pot_type' => [
                Rule::requiredIf(function () use ($productId) {
                    $productType = Product::findOrFail($productId)->product_type;
                    return $productType === 'plant';
                }),
            ],
        ]);

        $customer = Auth::guard('customer-api')->user();

        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shoppingCart = $customer->shoppingCart;

        if (!$shoppingCart) {
            return response()->json(['error' => 'Customer does not have a shopping cart'], 404);
        }

        $product = Product::findOrFail($productId);

        if ($product->quantity < $request->input('quantity')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient product quantity',
            ], 400);
        }

        $cartItem = $shoppingCart->cartItems()->create([
            'quantity' => $request->input('quantity'),
            'is_gift' => (bool) $request->input('is_gift', false),
            'pot_type' => $request->input('pot_type', null),
            'product_id' => $productId,
        ]);

        $product->quantity -= $request->input('quantity');
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'shoppingCart' => $shoppingCart->load(['cartItems.product' => function ($query) {
                $query->select('id', 'name', 'price', 'product_type');
            }]),
        ]);
    }

    public function removeFromCart($cartItemId)
    {
        $customer = Auth::guard('customer-api')->user();

        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shoppingCart = $customer->shoppingCart;

        if (!$shoppingCart) {
            return response()->json(['error' => 'Customer does not have a shopping cart'], 404);
        }

        $cartItem = $shoppingCart->cartItems()->findOrFail($cartItemId);

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed from cart successfully']);
    }
}
