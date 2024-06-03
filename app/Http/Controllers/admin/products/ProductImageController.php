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
        $product = Product::findOrFail($productId);

        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Assuming maximum 
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('product_images', $imageName, 'public');

            $productImage = new ProductImage();
            $productImage->product_id = $productId;
            $productImage->image = $imageName;
            $productImage->save();
        }
        $productWithImages = Product::with('images')->find($productId);
        return response()->json(['message' => 'Images uploaded successfully', 'product' => $productWithImages], 201);
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
