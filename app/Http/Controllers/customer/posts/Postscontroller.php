<?php

namespace App\Http\Controllers\customer\posts;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post ; 
use App\Models\Comment ; 
use Carbon\Carbon;

class Postscontroller extends Controller
{
    public function showposts()
    {
        $post = Post::with('comments.customer','customer')->get();
    if ($post->isEmpty()) {
       return response()->json(['message' => 'No posts found.'], 404);
   }
   return response()->json($post, 200);
    }


    public function createPost(Request $request)
    {
        $customer = auth()->guard('customer-api')->user();

        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image',
           
           

        ]);
        $image = $request->file('image');
        if ($image) {
            $imagename = time() . '.' . $image->extension();
            $image->move(public_path('uploads/posts/cover'), $imagename);
        } else {
            $imagename = null; 
        }
    
        $postData = [
            'content' => $request->input('content'),
            'image' => url('uploads/posts/cover/' . $imagename),
            'customer_id' => $customer->id,
            'post_date' => Carbon::now(),
        ];
        $post = Post::create($postData);
    
        
        return response()->json($post, 201);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->comments()->delete();
        $post->delete();

        return response()->json(['message' => 'post deleted successfully']);
    }
        /**
     * Search for product types by name.
     */
    public function search(string $content)
    {
        $posts = Post::where('content', 'like', '%' . $content . '%')
        ->with('comments.customer', 'customer')
        ->get();

        return response()->json($posts);
    }
    
    Public function likePost($id)
    {
        $post = Post::findOrFail($id);
        $customer = auth()->guard('customer-api')->user();
    
        // Check if the post is already liked by this customer
        if ($post->likedByCustomers()->where('customer_id', $customer->id)->exists()) {
            return response()->json(['message' => 'Post already liked'], 400);
        }
    
        // Add the like relationship
        $post->likedByCustomers()->attach($customer->id);
    
        // Increment the like count
        $post->like_count += 1;
        $post->save();
    
        return response()->json(['message' => 'Post liked successfully']);
    }
    public function unlikePost($id)
{
    $post = Post::findOrFail($id);
    $customer = auth()->guard('customer-api')->user();

    // Check if the post is liked by this customer
    if (!$post->likedByCustomers()->where('customer_id', $customer->id)->exists()) {
        return response()->json(['message' => 'Post not liked yet'], 400);
    }

    // Remove the like relationship
    $post->likedByCustomers()->detach($customer->id);

    // Decrement the like count
    $post->like_count -= 1;
    $post->save();

    return response()->json(['message' => 'Post unliked successfully']);
}
    public function ShowLikedPosts ()
    {

        $customer = auth('customer-api')->user();
        $likedPosts = $customer->likedPosts()->with('comments.customer','customer')->get();
        if ($likedPosts-> isEmpty()) {
            return response()->json(['message' => 'There are no liked posts']);
        }
     
        return response()->json(['liked_posts' => $likedPosts]);

    }
}
