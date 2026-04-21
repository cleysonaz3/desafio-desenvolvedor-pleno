<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use RuntimeException;

class JwtService
{
    public function ttl(): int
    {
        return (int) config('jwt.ttl', 3600);
    }

    public function generateToken(User $user): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + $this->ttl();

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $payload = [
            'iss' => config('jwt.issuer'),
            'sub' => $user->getKey(),
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $expiresAt,
            'ver' => $user->token_version,
        ];

        $encodedHeader = $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $this->secret(), true);

        return $encodedHeader.'.'.$encodedPayload.'.'.$this->base64UrlEncode($signature);
    }

    /**
     * @return array<string, mixed>
     */
    public function decode(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new AuthenticationException('Token JWT inválido.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $signature = $this->base64UrlDecode($encodedSignature);
        $expectedSignature = hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $this->secret(), true);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new AuthenticationException('Assinatura do token é inválida.');
        }

        $header = json_decode($this->base64UrlDecode($encodedHeader), true, 512, JSON_THROW_ON_ERROR);
        $payload = json_decode($this->base64UrlDecode($encodedPayload), true, 512, JSON_THROW_ON_ERROR);

        if (($header['alg'] ?? null) !== 'HS256') {
            throw new AuthenticationException('Algoritmo JWT não suportado.');
        }

        $now = time();

        if (($payload['nbf'] ?? 0) > $now || ($payload['exp'] ?? 0) < $now) {
            throw new AuthenticationException('Token expirado ou ainda não válido.');
        }

        return $payload;
    }

    public function authenticate(string $token): User
    {
        $payload = $this->decode($token);
        $userId = $payload['sub'] ?? null;

        if (! is_int($userId) && ! ctype_digit((string) $userId)) {
            throw new AuthenticationException('Token JWT inválido.');
        }

        $user = User::query()->find((int) $userId);

        if (! $user) {
            throw new AuthenticationException('Usuário do token não foi encontrado.');
        }

        if ($user->token_version !== (int) ($payload['ver'] ?? -1)) {
            throw new AuthenticationException('Token revogado.');
        }

        return $user;
    }

    private function secret(): string
    {
        $appKey = (string) config('app.key');

        if ($appKey === '') {
            throw new RuntimeException('APP_KEY não configurada.');
        }

        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);

            if ($decoded === false || $decoded === '') {
                throw new RuntimeException('APP_KEY base64 inválida.');
            }

            return $decoded;
        }

        return $appKey;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = strlen($value) % 4;

        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        return base64_decode(strtr($value, '-_', '+/'), true) ?: '';
    }
}
