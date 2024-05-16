<?php

namespace App\Http\Controllers\admin\product;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created product with tags in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'tags' => 'required|array', 
        ]);
    
        
        $product = Product::create($request->except('tags'));
    
        $product->tags()->attach($request->input('tags'));
    
      
        $productWithTags = Product::with('tags')->find($product->id);
    
        return response()->json($productWithTags, 201);
    }
    

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with('tags')->find($id);
        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'tags' => 'required|array', // Assuming tags are sent as an array of tag IDs
        ]);

        $product->update($request->except('tags'));
        $product->tags()->sync($request->input('tags'));

        return response()->json($product, 200);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->tags()->detach(); 
        $product->delete();

        return response()->json(null, 204);
    }
        /**
     * Search for product types by name.
     */
    public function search(string $name)
    {
        $product = Product::where('name', 'like', '%' . $name . '%')->get();
        return response()->json($product);
    }
}
