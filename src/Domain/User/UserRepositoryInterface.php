<?php
namespace Src\Domain\User;

interface UserRepositoryInterface {
    public function save(User $user): void;
    public function findByUsername(string $username): ?User;
    public function all(): array;
}
