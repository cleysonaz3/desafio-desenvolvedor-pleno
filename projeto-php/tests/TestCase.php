<?php

namespace Tests;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! config('app.key')) {
            config([
                'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            ]);
        }
    }

    protected function authHeaders(?string $token = null): array
    {
        if ($token === null) {
            $user = User::factory()->create();
            $token = app(AuthService::class)->issueToken($user);
        }

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }
}
