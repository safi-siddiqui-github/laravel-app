<?php

namespace App\Http\Resources\PersonalAccessToken;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

class PersonalAccessTokenResource extends JsonResource
{

    public function __construct(
        public NewAccessToken $token,
    ) {}

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->token->accessToken->id,
            // 'tokenable' => $this->tokenable,
            'name' => $this->token->accessToken->name,
            'token' => $this->token->plainTextToken,
            'abilities' => $this->token->accessToken->abilities,
            'last_used_at' => $this->token->accessToken->last_used_at,
            'expires_at' => $this->token->accessToken->expires_at,
            'created_at' => $this->token->accessToken->created_at,
            'updated_at' => $this->token->accessToken->updated_at,
        ];
    }
}
