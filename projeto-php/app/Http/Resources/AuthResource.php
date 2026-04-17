<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'message' => $this['message'],
            'data' => [
                'token' => $this['token'],
                'token_type' => $this['token_type'],
                'expires_in' => $this['expires_in'],
                'user' => new UserResource($this['user']),
            ],
        ];
    }
}
