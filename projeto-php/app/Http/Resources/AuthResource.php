<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'user' => new UserResource($this['user']),
        ];

        if (isset($this['token']) && $this['token'] !== null) {
            $data['token'] = $this['token'];
            $data['token_type'] = $this['token_type'];
            $data['expires_in'] = $this['expires_in'];
        }

        return [
            'message' => $this['message'],
            'data' => $data,
        ];
    }
}
