<?php
namespace Src\Infrastructure\Auth;

use Src\Domain\Auth\TokenService;
use Src\Infrastructure\Persistence\EloquentUserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTTokenService implements TokenService {

    private EloquentUserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new EloquentUserRepository();
    }

    public function generateToken($user): string {
        $payload = [
            'username' => $user->getUsername(),
            'role' => $user->getRole(),
            'iat' => time(),
            'exp' => time() + 3600
        ];
        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function validateToken(string $token) {
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
