<?php

namespace App\Http\Controllers\admin\products;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found.'], 404);
        }
    
   
        $products = Product::with(['tags' => function ($query) {
            $query->select('tags.id', 'name');
        }])->get();
    
      
        return response()->json($products, 200);
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
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'product_type' => 'required|string|exists:producttypes,name'
        ]);
        $tagNames = $request->input('tags');
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $tag = Tag::where('name', $tagName)->first();
            if ($tag) {
                $tagIds[] = $tag->id;
            } 
        }
        $product = Product::create($request->except('tags'));

      
        $tagNames = $request->input('tags');
        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $product->tags()->attach($tag->id);
        }
    
      
        $productWithTags = Product::with(['tags' => function ($query) {
            $query->select('tags.id', 'name');
        }])->find($product->id);
        
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
            'name' => 'string',
            'description' => 'nullable|string',
            'tags' => 'array',
            'price' => 'numeric',
            'quantity' => 'integer',
            'product_type' => 'string|exists:producttypes,name'
        ]);
    
       
        $product->update($request->only(['name', 'description', 'price', 'quantity', 'product_type']));
    
      
        if ($request->has('tags')) {
           
            $tagIds = [];
            foreach ($request->input('tags') as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $product->tags()->attach($tagIds);
        }
    
        $product = Product::with('tags')->find($id);
        return response()->json($product);
    }
    
    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->tags()->detach(); 
        $product->delete();

        return response()->json(['message' => 'product deleted successfully']);
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
