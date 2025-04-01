<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'language')]
    private Collection $questions;

    /**
     * @var Collection<int, LanguageManagement>
     */
    #[ORM\OneToMany(targetEntity: LanguageManagement::class, mappedBy: 'language')]
    private Collection $languageManagement;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->languageManagement = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setLanguage($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getLanguage() === $this) {
                $question->setLanguage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LanguageManagement>
     */
    public function getLanguageManagement(): Collection
    {
        return $this->languageManagement;
    }

    public function addLanguageManagement(LanguageManagement $languageManagement): static
    {
        if (!$this->languageManagement->contains($languageManagement)) {
            $this->languageManagement->add($languageManagement);
            $languageManagement->setLanguage($this);
        }

        return $this;
    }

    public function removeLanguageManagement(LanguageManagement $languageManagement): static
    {
        if ($this->languageManagement->removeElement($languageManagement)) {
            // set the owning side to null (unless already changed)
            if ($languageManagement->getLanguage() === $this) {
                $languageManagement->setLanguage(null);
            }
        }

        return $this;
    }
}
