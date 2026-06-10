<?php

namespace App\Http\Controllers;

use App\Enums\Post\PostStatusEnum;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(): Collection
    {
        request()->validate([
            'user_id' => 'sometimes|exists:users,id',
            'title' => 'sometimes|string',
            'excerpt' => 'sometimes|string|min:8',
            'status' => ['sometimes', Rule::enum(PostStatusEnum::class)],
        ]);

        $user_id = request()->input('user_id');

        $posts  = Post::query()
            ->when($user_id, function (Builder $query, string $user_id) {
                $query->where('user_id', $user_id);
            })
            ->get();

        return $posts;
    }

    protected function slugify(string $title): string
    {
        $words =  Str::words($title, 5);
        $slug = Str::of($words)->slug('-');
        $slug .= "-" . Str::ulid();

        return $slug;
    }

    public function store(): Post
    {
        request()->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'excerpt' => 'required|string|min:8',
            'content' => 'required|string|min:8',
            'status' => ['required', Rule::enum(PostStatusEnum::class)],
        ]);

        $post  = new Post();
        $post->user_id = request()->input('user_id');
        $post->slug = $this->slugify(request()->input('title'));
        $post->title = request()->input('title');
        $post->status = request()->input('status');
        $post->excerpt = request()->input('excerpt');
        $post->content = request()->input('content');
        $post->save();

        return $post;
    }

    public function deleteFN(): void
    {
        request()->validate([
            'id' => 'required|exists:posts,id',
        ]);

        Post::findOrFail(request()->input('id'))->delete();
    }

    public function deleteUserPost(): void
    {
        request()->validate([
            'user_id' => 'required|exists:users,id',
            'id' => 'required|exists:posts,id',
        ]);

        Post::where([
            ['id', request()->input('id')],
            ['user_id', request()->input('user_id')],
        ])->delete();
    }
}
