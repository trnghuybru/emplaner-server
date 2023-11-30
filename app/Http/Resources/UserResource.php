<?php

namespace App\Http\Resources;

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
        return [
            "token" => $this->token,
            "user" => [
                "id"=> $this->id,
                "name"=> $this->name,
                "email"=> $this->email,
                "job" => $this->job,
                "email_verified_at"=> $this->email_verified_at,
                "avatar" => $this->avatar
            ]
        ];
    }
}
