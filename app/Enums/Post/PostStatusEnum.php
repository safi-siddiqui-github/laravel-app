<?php

namespace App\Enums\Post;

enum PostStatusEnum: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case REVIEW = 'REVIEW';
    case APPROVED = 'APPROVED';
    case SCHEDULED = 'SCHEDULED';
    case PUBLISHED = 'PUBLISHED';
    case ARCHIVED = 'ARCHIVED';
}
