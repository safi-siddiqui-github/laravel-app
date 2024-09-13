<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'tags')->paginate(3);

        return view('posts', [
            'posts' => $posts,
        ]);
    }
}
