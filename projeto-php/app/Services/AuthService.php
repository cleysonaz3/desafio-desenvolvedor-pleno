<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private readonly JwtService $jwtService)
    {
    }

    /**
     * @param  array{name: string, email: string, password: string}  $data
     * @return array<string, mixed>
     */
    public function register(array $data): array
    {
        $user = User::query()->create($data);

        return $this->buildAuthPayload($user, 'Usuário registrado com sucesso.');
    }

    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array<string, mixed>
     */
    public function login(array $credentials): array
    {
        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais informadas são inválidas.'],
            ]);
        }

        return $this->buildAuthPayload($user, 'Login realizado com sucesso.');
    }

    public function logout(User $user): void
    {
        $user->increment('token_version');
        $user->refresh();
    }

    public function issueToken(User $user): string
    {
        return $this->jwtService->generateToken($user);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAuthPayload(User $user, string $message): array
    {
        return [
            'message' => $message,
            'token' => $this->issueToken($user),
            'token_type' => 'Bearer',
            'expires_in' => $this->jwtService->ttl(),
            'user' => $user,
        ];
    }
}
