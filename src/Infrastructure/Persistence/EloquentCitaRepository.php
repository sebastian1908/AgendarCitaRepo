<?php
namespace Src\Infrastructure\Persistence;

use Src\Domain\Cita\Cita;
use Src\Domain\Cita\CitaRepositoryInterface;
use App\Models\Cita as EloquentCita;

class EloquentCitaRepository implements CitaRepositoryInterface {

    public function save(Cita $cita): void {
        EloquentCita::create([
            'id' => $cita->getId(),
            'title' => $cita->getTitle(),
            'description' => $cita->getDescription(),
            'date' => $cita->getDate(),
            'user' => $cita->getUser()
        ]);
    }

    public function all(): array {
        $records = EloquentCita::all();
        $citas = [];
        foreach ($records as $record) {
            $citas[] = new Cita($record->id, $record->title, $record->description, $record->date, $record->user);
        }
        return $citas;
    }

    public function findByUser(string $username): array {
        $records = EloquentCita::where('user', $username)->get();
        $citas = [];
        foreach ($records as $record) {
            $citas[] = new Cita($record->id, $record->title, $record->description, $record->date, $record->user);
        }
        return $citas;
    }
}
