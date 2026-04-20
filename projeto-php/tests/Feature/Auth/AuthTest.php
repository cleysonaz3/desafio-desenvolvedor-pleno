<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function test_user_can_register_and_receive_a_jwt_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Novo Usuário',
            'email' => 'novo@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $loginResponse->json('data.token');

        $this->postJson('/api/logout', [], $this->authHeaders($token))
            ->assertOk()
            ->assertJson([
                'message' => 'Logout realizado com sucesso.',
            ]);

        $this->getJson('/api/categories', $this->authHeaders($token))
            ->assertUnauthorized();
    }

    public function test_user_can_authenticate_with_http_only_cookie_for_frontend_flow(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ], [
            'X-Frontend-Auth' => 'cookie',
        ]);

        $cookieName = config('jwt.cookie_name');
        $cookieValue = collect($loginResponse->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === $cookieName)
            ?->getValue();

        $this->assertNotNull($cookieValue);

        $loginResponse
            ->assertOk()
            ->assertJsonMissingPath('data.token')
            ->assertJsonPath('data.user.email', $user->email);

        $this->call(
            'GET',
            '/api/me',
            [],
            [$cookieName => $cookieValue],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        )
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->call(
            'POST',
            '/api/logout',
            [],
            [$cookieName => $cookieValue],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        )
            ->assertOk();
    }

    public function test_protected_routes_require_a_valid_token(): void
    {
        $this->getJson('/api/categories')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Token de acesso não informado.',
            ]);
    }
}
