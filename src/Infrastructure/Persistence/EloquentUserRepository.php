<?php

namespace Src\Infrastructure\Persistence;

use Src\Domain\User\User;
use Src\Domain\User\UserRepositoryInterface;
use App\Models\User as EloquentUser;    

class EloquentUserRepository implements UserRepositoryInterface {

    public function save(User $user): void {
        EloquentUser::updateOrCreate(
            ['username' => $user->getUsername()],
            ['password' => $user->getPassword(), 'role' => $user->getRole()]
        );
    }

    public function findByUsername(string $username): ?User {
        $record = EloquentUser::where('username', $username)->first();
        if (!$record) return null;

        return new User($record->username, $record->password, $record->role);
    }

    public function all(): array {
        $records = EloquentUser::all();
        $users = [];
        foreach ($records as $record) {
            $users[] = new User($record->username, $record->password, $record->role);
        }
        return $users;
    }
}
