<?php

namespace App\Http\Controllers\customer\favs;
use  App\Models\Customer; 
use  App\Models\Product; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LikeProductsController extends Controller
{
    
        public function likeProduct($productId)
        {
            $customer = auth('customer-api')->user();
    
           
            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
    
            if ($customer->likedProducts()->where('product_id', $productId)->exists()) {
                return response()->json(['message' => 'You already liked this product'], 400);
            }
    
            
            $customer->likedProducts()->attach($productId);
    
          
            $product->incrementLikeCount();
    
            return response()->json(['message' => 'Product liked successfully']);
        
 

    }
    public function UnlikeProduct($productId)
        {
            $customer = auth('customer-api')->user();
    
           
            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
    
            if (!$customer->likedProducts()->where('product_id', $productId)->exists()) {
                return response()->json(['message' => 'You have not liked this product'], 400);
            }

            $customer->likedProducts()->detach($productId);
    
            $product->decrementLikeCount();
    
            return response()->json(['message' => 'Product unliked successfully']);
        
 

    }
    public function ShowLikedProducts ()
    {

        $customer = auth('customer-api')->user();
        $likedProducts = $customer->likedProducts()->get();
        if ($likedProducts-> isEmpty()) {
            return response()->json(['message' => 'There are no liked products']);
        }
        $likedProducts->each(function ($product) {
            $product->first_image = $product->images->first() ? $product->images->first()->image : null;
        });
        return response()->json(['liked_products' => $likedProducts]);

    }
}
