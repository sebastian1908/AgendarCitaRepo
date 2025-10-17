<?php
namespace Src\Domain\Auth;

interface TokenService {
    public function generateToken($user): string;
    public function validateToken(string $token);
}
