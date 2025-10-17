<?php
namespace Src\Domain\Cita;

interface CitaRepositoryInterface {
    public function save(Cita $cita): void;
    public function all(): array;
    public function findByUser(string $username): array;
}
