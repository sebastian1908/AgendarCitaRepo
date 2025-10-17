<?php
namespace Src\Application\User;

use Src\Domain\User\User;
use Src\Domain\User\UserRepositoryInterface;
use Src\Domain\User\Role;

class RegisterUserService {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register(string $username, string $password): User {
        $user = new User($username, password_hash($password, PASSWORD_BCRYPT), Role::OPERARIO->value);
        $this->userRepository->save($user);
        return $user;
    }
}
