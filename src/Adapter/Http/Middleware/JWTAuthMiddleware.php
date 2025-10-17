<?php
namespace Src\Adapter\Http\Middleware;

use Closure;
use Src\Infrastructure\Auth\JWTTokenService;

class JWTAuthMiddleware {

    private JWTTokenService $tokenService;

    public function __construct() {
        $this->tokenService = new JWTTokenService();
    }

    public function handle($request, Closure $next) {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token requerido'], 401);
        }

        $token = substr($authHeader, 7);
        $decoded = $this->tokenService->validateToken($token);

        if (!$decoded) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        $request->user = (object)[
            'username' => $decoded->username,
            'role' => $decoded->role
        ];

        return $next($request);
    }
}
