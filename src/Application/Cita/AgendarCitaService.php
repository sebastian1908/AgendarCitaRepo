<?php
namespace Src\Application\Cita;

use Src\Domain\Cita\CitaRepositoryInterface;
use Src\Domain\Cita\Cita;
use Illuminate\Support\Str;

class AgendarCitaService {

    private CitaRepositoryInterface $repo;

    public function __construct(CitaRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function agendar(array $data, string $username): Cita {
        $cita = new Cita(
            Str::uuid()->toString(),
            $data['title'],
            $data['description'],
            $data['date'],
            $username
        );
        $this->repo->save($cita);
        return $cita;
    }

    public function listarCitas(string $username): array {
        return $this->repo->findByUser($username);
    }
}
