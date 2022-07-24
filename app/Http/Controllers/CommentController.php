<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);
        if(!$post){
            return response([
                'message' => 'Post not found',
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission Denied'
            ], 403);
        }

        return response([
            'comment' => $post->comments()->with('user:id,name,image')->get()
        ], 200);
    }    

    public function store($id, Request $request)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $post->id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Comment Created'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);
        
        if(!$comment){
            return response([
                'message' => 'Comment not found!'
            ], 403);
        }
        
        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permisson Denied'
            ], 403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);

        return response([
            'message' => 'Comment Updated'
        ], 200);
    }

    public function delete($id)
    {
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ], 403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission Denied'
            ], 403);
        }

        $comment->delete();

        return response([
            'message' => 'Comment deleted!'
        ], 200);
    }
}
