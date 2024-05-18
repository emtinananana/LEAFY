<?php

namespace App\Http\Controllers\admin\productType;

use App\Models\ProductType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CRUDController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productTypes = ProductType::all();
        if ($productTypes->isEmpty()) {
            return response()->json(['message' => 'No product types found.'], 404);
        }
    
        return response()->json($productTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $productType = ProductType::create($request->all());

        return response()->json($productType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productType = ProductType::find($id);
        if (!$productType) {
            return response()->json(['error' => 'Product type not found'], 404);
        }
        return response()->json($productType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $productType = ProductType::find($id);
        if (!$productType) {
            return response()->json(['error' => 'Product type not found'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
    
        
        $productType->update($request->all());
    
 
        return response()->json($productType);
    }
    
    

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $productType = ProductType::find($id);
        if (!$productType) {
            return response()->json(['error' => 'Product type not found'], 404);
        }

        $productType->delete();

        return response()->json(['message' => 'Product type deleted successfully']);
    }

    /**
     * Search for product types by name.
     */
    public function search(string $name)
    {
        $productTypes = ProductType::where('name', 'like', '%' . $name . '%')->get();
        return response()->json($productTypes);
    }
}
