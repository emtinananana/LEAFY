<?php
namespace App\Http\Controllers\admin\products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    public function store(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (!$request->hasFile('images')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

     
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads/products/avatars', $imageName, 'my_files');

            ProductImage::create([
                'product_id' => $productId,
                'image' => $imageName,
            ]);

            
        }

        $productWithImages = Product::with('images')->find($productId);

        return response()->json([
            'message' => 'Images uploaded successfully',
            'product' => $productWithImages,
           
        ], 201);
    }

    public function destroy($productId, $imageId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $productImage = ProductImage::where('product_id', $productId)->find($imageId);
        if (!$productImage) {
            return response()->json(['message' => 'Image not found or does not belong to this product'], 404);
        }

        $imagePath = 'uploads/products/avatars/' . $productImage->image;

        if (Storage::disk('my_files')->exists($imagePath)) {
            Storage::disk('my_files')->delete($imagePath);
        }

   
        $productImage->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
    public function update(Request $request, $productId, $imageId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        $productImage = ProductImage::where('product_id', $productId)->find($imageId);
        if (!$productImage) {
            return response()->json(['message' => 'Image not found or does not belong to this product'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        if ($request->hasFile('image')) {
          
            Storage::disk('my_files')->delete($productImage->image);
    
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('product_images', $imageName, 'my_files');
    
           
            $productImage->update(['image' => $path]);
    
            return response()->json(['message' => 'Image updated successfully', 'image' => $productImage]);
        } else {
            return response()->json(['message' => 'No image file uploaded'], 400);
        }
    }

    public function showAllImages($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $images = ProductImage::where('product_id', $productId)->get();
        if ($images->isEmpty()) {
            return response()->json(['message' => 'No images found for this product']);
        }
    
        return response()->json(['images' => $images]);
    }
}

