<?php
namespace Src\Domain\User;

class User {
    private string $username;
    private string $password;
    private string $role;

    public function __construct(string $username, string $password, string $role) {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function getUsername(): string { return $this->username; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): string { return $this->role; }

    public function setPassword(string $password): void { $this->password = $password; }
    public function setRole(string $role): void { $this->role = $role; }
}
