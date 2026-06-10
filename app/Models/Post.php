<?php

namespace App\Models;

use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Attributes\UseResourceCollection;
use Illuminate\Database\Eloquent\Model;

#[UseResource(PostResource::class)]
#[UseResourceCollection(PostCollection::class)]
class Post extends Model
{
    //
}
