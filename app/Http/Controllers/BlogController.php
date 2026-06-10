<?php

namespace App\Http\Controllers;

use App\Enums\Post\PostStatusEnum;
use App\Models\Post;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ResponseTrait;

    public PostController $postController;
    // public UserController $userController;
    // public OtpController $otpController;

    public function __construct()
    {
        $this->postController = new PostController();
        // $this->userController = new UserController();
        // $this->otpController = new OtpController();
    }

    public function getPosts(Request $request)
    {
        // $user = $request->user();

        // request()->mergeIfMissing([
        // 'user_id' => $user->id,
        // 'status' => PostStatusEnum::DRAFT->value,
        // ]);
        $posts = $this->postController->index();

        return $this->apiResponse(
            message: 'Posts Found',
            data: [
                'posts' => $posts->toResourceCollection(),
            ]
        );
    }

    public function getCurrentUserPosts(Request $request)
    {
        $user = $request->user();

        request()->mergeIfMissing([
            'user_id' => $user->id,
            // 'status' => PostStatusEnum::DRAFT->value,
        ]);
        $posts = $this->postController->index();

        return $this->apiResponse(
            message: 'Posts Found',
            data: [
                'posts' => $posts->toResourceCollection(),
            ]
        );
    }

    public function storePost(Request $request)
    {
        $user = $request->user();

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'status' => PostStatusEnum::DRAFT->value,
        ]);
        $post = $this->postController->store();

        return $this->apiResponse(
            message: 'Post Stored',
            data: [
                'post' => $post->toResource(),
            ]
        );
    }

    public function deleteCurrentUserPost(Request $request)
    {
        $user = $request->user();

        request()->mergeIfMissing([
            'user_id' => $user->id,
        ]);
        $this->postController->deleteUserPost();

        return $this->apiResponse(
            message: 'Post Deleted',
        );
    }
}
