<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Category
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /** Translation key suffix, e.g. "books" => category.books */
    #[ORM\Column(length: 50, unique: true)]
    private string $slug = '';

    #[ORM\Column(length: 100)]
    private string $nameNl = '';

    #[ORM\Column(length: 100)]
    private string $nameEn = '';

    #[ORM\Column(length: 100)]
    private string $nameAr = '';

    public function getId(): ?int { return $this->id; }
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $v): static { $this->slug = $v; return $this; }
    public function getNameNl(): string { return $this->nameNl; }
    public function setNameNl(string $v): static { $this->nameNl = $v; return $this; }
    public function getNameEn(): string { return $this->nameEn; }
    public function setNameEn(string $v): static { $this->nameEn = $v; return $this; }
    public function getNameAr(): string { return $this->nameAr; }
    public function setNameAr(string $v): static { $this->nameAr = $v; return $this; }

    public function getName(string $locale): string
    {
        return match ($locale) {
            'en' => $this->nameEn,
            'ar' => $this->nameAr,
            default => $this->nameNl,
        } ?: $this->nameNl;
    }

    public function __toString(): string { return $this->nameNl; }
}
