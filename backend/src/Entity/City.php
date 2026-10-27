<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** A city or suburb. Start: Deventer and surroundings; more can be added in the admin. */
#[ORM\Entity]
class City
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $name = '';

    /** Main city this place belongs to (e.g. Deventer for Colmschate). */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $region = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): static { $this->name = $v; return $this; }
    public function getRegion(): ?string { return $this->region; }
    public function setRegion(?string $v): static { $this->region = $v; return $this; }
    public function __toString(): string { return $this->name; }
}
