<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        $like = $post->likes()
            ->where('user_id', auth()->user()->id)
            ->first();
        
        if(!$like){
            Like::create([
                'post_id' => $post->id,
                'user_id' => auth()->user()->id
            ]);

            return response([
                'message' => 'Post Liked'
            ], 200);
        }

        $like->delete();

        return response([
            'message' => 'Post unliked'
        ], 200);
    }
}
