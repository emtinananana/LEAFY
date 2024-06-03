<?php

namespace App\Http\Controllers\customer\posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Comment;
use Carbon\Carbon;

class CommentController extends Controller
{
    public function showComments($postId)
    {
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->get();

        return response()->json($comments);
    }

    public function addComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $customer = auth()->guard('customer-api')->user();
        $post = Post::findOrFail($postId);

        $commentData = [
            'customer_id' => $customer->id,
            'post_id' => $post->id,
            'content' => $request->input('content'),
            'comment_date' => Carbon::now(),
        ];

        $comment = Comment::create($commentData);

        return response()->json($comment, 201);
    }
}
