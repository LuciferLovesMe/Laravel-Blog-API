<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')
                ->with('user:id,name,image')
                ->withCount('comments', 'likes')
                ->with('likes', function($like){
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')
                        ->get();
                })
                ->get()
        ], 200);
    }
    
    public function show($id)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')
                ->with('likes', function($like){
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')
                        ->get();
                })
                ->get()
        ], 200);
    }

    // Create a Post
    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->storeImage($request->image, 'posts');

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        return response([
            'message' => 'Post Created',
            'post' => $post
        ], 200);
    }

    // Update a Post
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post Not Found',
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission Denied!',
            ], 403);
        }

        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $attrs['body']
        ]);

        return response([
            'message' => 'Post Updated',
            'post' => $post
        ], 200);
    }

    // Delete a Post
    public function delete(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission Denied'
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();
    }
}
