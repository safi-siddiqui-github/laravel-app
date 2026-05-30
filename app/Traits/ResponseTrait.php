<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    protected function apiResponse(
        bool $success = true,
        string $message = '',
        array $data = [],
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ]);
    }
}
