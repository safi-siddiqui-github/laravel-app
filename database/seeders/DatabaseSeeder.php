<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Tag;
use App\Models\PostTag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = fake()->name();
        $user->email = fake()->email();
        $user->password = fake()->password();
        $user->save();

        for ($i = 0; $i < 10; $i++) {
            $post = new Post();
            $post->user_id = $user->id;
            $post->title = fake()->word();
            $post->description = fake()->text();
            $post->save();

            $tag = new Tag();
            $tag->name = fake()->word();
            $tag->save();

            $post->tags()->attach($tag->id);
        }
    }
}
