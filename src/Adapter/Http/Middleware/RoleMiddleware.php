<?php
// para roles mas adelante
namespace Src\Adapter\Http\Middleware;

use Closure;

class RoleMiddleware {

    private string $requiredRole;

    public function __construct(string $requiredRole = 'admin') {
        $this->requiredRole = $requiredRole;
    }

    public function handle($request, Closure $next, $role = null) {
        $roleToCheck = $role ?? $this->requiredRole;

        if (!isset($request->user) || $request->user->role !== $roleToCheck) {
            return response()->json([
                'status' => 403,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        return $next($request);
    }
}
