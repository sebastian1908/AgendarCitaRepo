<?php
namespace Src\Application\User;

use Src\Domain\User\User;
use Src\Domain\User\UserRepositoryInterface;

class CreateUserService {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data, $currentUser): User {
        if ($currentUser->getRole() !== "ADMIN") {
            throw new \Exception("No tienes permisos para crear usuarios");
        }
        $user = new User($data['username'], password_hash($data['password'], PASSWORD_BCRYPT), $data['role']);
        $this->userRepository->save($user);
        return $user;
    }
}
