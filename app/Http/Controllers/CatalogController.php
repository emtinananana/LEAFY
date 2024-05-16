<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }
    public function showByType($type)
    {
        
        $products = Product::where('type', $type)->get();
    
        return response()->json(['products' => $products]);
    }
    public function search(Request $request)
{
    $query = $request->input('query');
    $products = Product::where('name', 'like', "%$query%")->get();
    
    return response()->json(['products' => $products]);
}
public function filter(Request $request)
{
    $tags = $request->input('tags'); 
    $products = Product::whereHas('tags', function ($query) use ($tags) {
        $query->whereIn('name', $tags);
    })->get();
    
    return response()->json(['products' => $products]);
}
}
