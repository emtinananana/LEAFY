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
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imagefile) {
                $destinationPath = public_path('uploads/products/avatars');
            
                // Move the image to the specified directory
                $imagefile->move($destinationPath);
                
                // Save the image path to the database
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = 'uploads/products/avatars/' ; // Store the relative path
                $productImage->save();
    
                $uploadedImages[] = $productImage;
            }}

        return response()->json(['message' => 'Images uploaded successfully', 'images' => $uploadedImages], 201);
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

        Storage::disk('public')->delete($productImage->image); // Adjusted field name

        $productImage->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function update(Request $request, $productId, $imageId)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $productImage = ProductImage::where('product_id', $productId)->find($imageId);
        if (!$productImage) {
            return response()->json(['message' => 'Image not found or does not belong to this product'], 404);
        }

        Storage::disk('public')->delete($productImage->image);

        $path = $request->file('image')->store('product_images', 'public');

        $productImage->image = $path; // Adjusted field name
        $productImage->save();

        return response()->json(['message' => 'Image updated successfully', 'image' => $productImage]);
    }
}
