<?php
namespace Src\Domain\Cita;

class Cita {
    private string $id;
    private string $title;
    private string $description;
    private string $date;
    private string $user;

    public function __construct(string $id, string $title, string $description, string $date, string $user) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->user = $user;
    }

    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getDate(): string { return $this->date; }
    public function getUser(): string { return $this->user; }
}
