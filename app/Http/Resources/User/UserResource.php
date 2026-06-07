<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'email_verified_at' => $this->email_verified_at,
            // 'password' => null,
            // 'remember_token' => null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'initials' => $this->initials(),
            'can_reset_password' => $this->can_reset_password,
        ];
    }
}
