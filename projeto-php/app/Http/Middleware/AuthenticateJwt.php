<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticateJwt
{
    public function __construct(private readonly JwtService $jwtService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?: $request->cookie(config('jwt.cookie_name'));

        if (! $token) {
            throw new AuthenticationException('Token de acesso não informado.');
        }

        try {
            $user = $this->jwtService->authenticate($token);
        } catch (AuthenticationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new AuthenticationException('Token JWT inválido.');
        }

        Auth::guard('web')->setUser($user);
        $request->setUserResolver(static fn () => $user);

        return $next($request);
    }
}
