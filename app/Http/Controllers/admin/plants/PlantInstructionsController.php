<?php

namespace App\Http\Controllers\admin\plants;

use App\Models\Product;
use App\Models\Tag;
use App\Models\PlantInstruction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
class PlantInstructionsController extends Controller
{
    /**
     * Display a listing of the plantsinstructions.
     */
    public function index()
    {
        $plantinstructions = PlantInstruction::all();
        if ($plantinstructions->isEmpty()) {
            return response()->json(['message' => 'No instructions found.'], 404);
        }
    
        return response()->json($plantinstructions, 200);
    }

    
    public function store(Request $request, $productid)
    {
        $product = Product::findOrFail($productid);
    
        if ($product->product_type == 'plant') {
            $validatedData = $request->validate([
                'instruction' => 'nullable|string',
               
            ]);
    
            $validatedData['product_id'] = $productid;
    
            $plantinstruction = PlantInstruction::create($validatedData);
    
            return response()->json($plantinstruction, 201);
        }
    
        return response()->json(['message' => 'Product is not of type plant.'], 422);
    }
    
    

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $plantinstruction = PlantInstruction::findOrFail($id);
        return response()->json($plantinstruction);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $plantInstruction = PlantInstruction::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'instruction' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $plantInstruction->update([
            'instruction' => $request->input('instruction'),
        ]);
    
        return response()->json($plantInstruction);
    }
    
    
    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $plantinstruction = PlantInstruction::findOrFail($id);
 
        $plantinstruction->delete();

        return response()->json(['message' => 'plant instruction deleted successfully']);
    }
        /**
     * Search for product types by name.
     */
 
}
