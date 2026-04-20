<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->authService->register($request->validated());

        return $this->withAuthenticationCookie(
            $request,
            (new AuthResource($this->formatPayloadForFrontend($request, $payload)))
                ->response()
                ->setStatusCode(201),
            $payload['token']
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $this->authService->login($request->validated());

        return $this->withAuthenticationCookie(
            $request,
            (new AuthResource($this->formatPayloadForFrontend($request, $payload)))
                ->response(),
            $payload['token']
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ])->withoutCookie($this->cookieName());
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function formatPayloadForFrontend(Request $request, array $payload): array
    {
        if (! $this->usesFrontendCookieAuth($request)) {
            return $payload;
        }

        return [
            'message' => $payload['message'],
            'user' => $payload['user'],
            'token' => null,
        ];
    }

    private function withAuthenticationCookie(Request $request, JsonResponse $response, string $token): JsonResponse
    {
        if (! $this->usesFrontendCookieAuth($request)) {
            return $response;
        }

        return $response->withCookie($this->makeAuthCookie($token));
    }

    private function usesFrontendCookieAuth(Request $request): bool
    {
        return $request->headers->get('X-Frontend-Auth') === 'cookie';
    }

    private function makeAuthCookie(string $token): Cookie
    {
        return cookie(
            $this->cookieName(),
            $token,
            (int) ceil(config('jwt.ttl') / 60),
            '/',
            null,
            (bool) config('jwt.cookie_secure'),
            true,
            false,
            (string) config('jwt.cookie_same_site', 'strict')
        );
    }

    private function cookieName(): string
    {
        return (string) config('jwt.cookie_name', 'catalogo_token');
    }
}
