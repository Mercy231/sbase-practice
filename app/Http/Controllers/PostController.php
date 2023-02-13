<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function postCreate(Request $request)
    {
        $validator = Validator::make($request->only('text', 'category', 'image'), [
            'text' => 'required|min:1',
            'category' => 'required',
            'image' => 'image'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        }
        if ($request->image) {
            $request->image->store('images', 'public');
            Post::create([
                'user_id' => Auth::user()->id,
                'category' => $request->category,
                'text' => $request->text,
                'image' => $request->file('image')->storePublicly('images', 'public'),
            ]);
        } else {
            Post::create([
                'user_id' => Auth::user()->id,
                'category' => $request->category,
                'text' => $request->text,
            ]);
        }
        return response()->json(['success' => true], 200);
    }

    public function getPosts()  //NEEDS TO UPDATE (MODEL RELATIONS?)
    {
        $posts = Post::latest()
        ->select('posts.*')
        ->get()
        ->toArray();

        foreach ($posts as $key => $post) {     //TOO BAD
            $allComments = Comment::where('post_id', '=', $post['id'])->get();
            $comments = [];
            foreach ($allComments as $comment) {
                if ($comment->parent_id == null) {
                    $comments[] = $comment;
                }
            }

            foreach ($comments as $key2 => $comment) {
                $comments[$key2]['comments'] = $this->getReplies($comment);
            }
            $posts[$key]['comments'] = $comments;
        }

        return response()->json($posts, 200);
    }

    public function postEdit(Request $request)
    {
        $validator = Validator::make($request->only('text', 'category', 'image'), [
            'text' => 'required|min:1',
            'category' => 'required',
            'image' => 'image'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        if ($request->image) {
            Post::find($request->id)->update([
                'category' => $request->category,
                'text' => $request->text,
                'image' => $request->file('image')->storePublicly('images', 'public')
            ]);
        } else {
            Post::find($request->id)->update([
                'category' => $request->category,
                'text' => $request->text,
            ]);
        }
        return response()->json(['success' => true], 200);
    }

    public function postDelete ($id)
    {
        $post = Post::find($id);
        if ($post->image) {
            Storage::delete('/public/'.$post->image);
        }
        Post::find($id)->delete();
        return response()->json(['success' => true], 200);
    }

    public function createComment(Request $request)
    {
        $validator = Validator::make($request->only('text', 'image'), [
            'text' => 'required|min:1',
            'image' => 'image'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        if ($request->image) {
            $request->image->store('images', 'public');
            Comment::create([
                'user_id' => Auth::user()->id,
                'post_id' => $request->post_id,
                'text' => $request->text,
                'image' => $request->file('image')->storePublicly('images', 'public'),
            ]);
        } else {
            Comment::create([
                'user_id' => Auth::user()->id,
                'post_id' => $request->post_id,
                'text' => $request->text,
            ]);
        }
        return response()->json(['success' => true], 200);
    }

    public function createReply(Request $request)
    {
        $validator = Validator::make($request->only('text', 'image'), [
            'text' => 'required|min:1',
            'image' => 'image'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        if ($request->image) {
            $request->image->store('images', 'public');
            Comment::create([
                'user_id' => Auth::user()->id,
                'post_id' => $request->post_id,
                'parent_id' => $request->parent_id,
                'text' => $request->text,
                'image' => $request->file('image')->storePublicly('images', 'public'),
            ]);
        } else {
            Comment::create([
                'user_id' => Auth::user()->id,
                'post_id' => $request->post_id,
                'parent_id' => $request->parent_id,
                'text' => $request->text,
            ]);
        }
        return response()->json(['success' => true], 200);
    }

    public function replyEdit(Request $request)
    {
        $validator = Validator::make($request->only('text', 'image'), [
            'text' => 'required|min:1',
            'image' => 'image'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        if ($request->image) {
            $reply = Comment::find($request->id);
            Storage::delete('/public/'.$reply->image);

            Comment::find($request->id)->update([
                'text' => $request->text,
                'image' => $request->file('image')->storePublicly('images', 'public')
            ]);
        } else {
            Comment::find($request->id)->update(['text' => $request->text]);
        }
        return response()->json(['success' => true], 200);
    }

    public function replyDelete($id)
    {
        $comment = Comment::find($id);
        $this->deleteReplyChildren($comment);
    }

    public function deleteReplyChildren($comment)
    {
        $replies = Comment::where('parent_id', '=', $comment->id)->get();
        if ($replies) {
            foreach ($replies as $key => $reply) {
                $this->deleteReplyChildren($replies[$key]);
            }
        }
        if ($comment->image) {
            Storage::delete('/public/'.$comment->image);
        }
        Comment::find($comment->id)->delete();
    }

    public function getReplies($comment)
    {
        $collection = [null];
        $collection = Comment::where('parent_id', '=', $comment->id)->get();
        if ($collection) {
            foreach ($collection as $key => $comment) {
                $collection[$key]['comments'] = $this->getReplies($comment);
            }
        }
        return $collection;
    }
}
