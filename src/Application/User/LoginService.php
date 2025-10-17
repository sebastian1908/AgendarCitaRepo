<?php
namespace Src\Application\User;

use Src\Domain\User\UserRepositoryInterface;
use Src\Domain\Auth\TokenService;

class LoginService {
    private UserRepositoryInterface $userRepository;
    private TokenService $tokenService;

    public function __construct(UserRepositoryInterface $userRepo, TokenService $tokenService) {
        $this->userRepository = $userRepo;   
        $this->tokenService = $tokenService;
    }

    public function login(string $username, string $password): string {
        $user = $this->userRepository->findByUsername($username);
        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new \Exception("Credenciales inválidas");
        }

        return $this->tokenService->generateToken($user);
    }
}
