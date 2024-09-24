<?php

namespace App\Http\Controllers;

use App\Events\PostCreatedEvent;
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

        $post = new Post();
        $post->user_id = 1;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->save();

        PostCreatedEvent::dispatch($post);

        return to_route('post.index');
    }

    public function delete(string $id)
    {
        Post::find($id)->delete();
        return to_route('post.index');
    }
}
