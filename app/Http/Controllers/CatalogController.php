<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;


class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::with('images','tags')->get()->map(function ($product) {
            $product->first_image = $product->images->first() ? $product->images->first()->image : null;
        
            if ($product->product_type === 'Plant') {
                $product->load('plantInstruction');
            }
    
            return $product;
        });

        return response()->json($products);
    }

    public function showByType($type)
    {
        $products = Product::where('product_type', $type)->with('images')->get()->map(function ($product) {
            $product->first_image = $product->images->first() ? $product->images->first()->image : null;
         
        
        if ($product->product_type === 'Plant') {
            $product->load('plantInstruction');
        }

        return $product;
    });
        return response()->json(['products' => $products]);
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $products = Product::where('name', 'like', "%$name%")->with('images')->get()->map(function ($product) {
            $product->first_image = $product->images->first() ? $product->images->first()->image : null;
            return $product;
        });
    
        return response()->json(['products' => $products]);
    }

    public function filter(Request $request)
{
    $tags = $request->input('tags', []); 
    if (!is_array($tags)) {
       
        return response()->json(['message' => 'Tags parameter must be an array'], 400);
    }

    $products = Product::whereHas('tags', function ($query) use ($tags) {
        $query->whereIn('name', $tags);
    })->with('images')->get()->map(function ($product) {
        $product->first_image = $product->images->isNotEmpty() ? $product->images->first()->image : null;
        return $product;
    });

    return response()->json(['products' => $products]);
}

}
