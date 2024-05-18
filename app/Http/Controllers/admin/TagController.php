<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            
        ]);
        $tag = Tag::create($request->all());

        return response()->json(['tag' => $tag], 201);
    
    }

    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json(['tag' => $tag]);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:tags,name,' . $tag->id . '|max:255',
        ]);

        $tag->update($request->all());

        return response()->json(['tag' => $tag]);
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
