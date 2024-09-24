<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->with('user', 'tags')->paginate(3);

        return view('post.index', [
            'posts' => $posts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'string|required',
            'description' => 'string|required',
        ]);

        $post_new = new Post();
        $post_new->user_id = 1;
        $post_new->title = $request->input('title');
        $post_new->description = $request->input('description');
        $post_new->save();

        return to_route('post.index');
    }

    public function delete(string $id)
    {
        Post::find($id)->delete();
        return to_route('post.index');
    }
}
