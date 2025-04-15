<?php

namespace App\Entity;

use App\Repository\LanguageManagementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageManagementRepository::class)]
class LanguageManagement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'languageManagement')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'languageManagement')]
    private ?Language $language = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isProfessor = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function isProfessor(): bool
    {
    return $this->isProfessor;
    }

    public function setIsProfessor(bool $isProfessor): static
    {
    $this->isProfessor = $isProfessor;
    return $this;
    }   
}
