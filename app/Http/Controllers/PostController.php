<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function postCreate(Request $request)
    {
        $validator = Validator::make($request->only('text', 'category'), [
            'text' => 'required|min:1',
            'category' => 'required'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        } elseif (Auth::check()) {
            Post::create([
                'user_id' => Auth::user()->id,
                'category' => $request->category,
                'text' => $request->text,
            ]);
            return response()->json([
                'success' => true,
            ], 200);
        }
    }

    public function getPosts()
    {
        $response = Post::latest()->select('posts.*')->get();
        return response()->json($response, 200);
    }

    public function postEdit(Request $request)
    {
        $validator = Validator::make($request->only('text', 'category'), [
            'text' => 'required|min:1',
            'category' => 'required'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success'=> false,
                'error' => $validator->errors()->first(),
            ], 200);
        } elseif (Auth::check()) {
            Post::where('id', '=', $request->id)->update([
                'category' => $request->category,
                'text' => $request->text,
            ]);
            return response()->json([
                'success' => true,
            ], 200);
        }
    }

    public function postDelete ($id)
    {
        Post::where('id', '=', $id)->delete();
    }
}
