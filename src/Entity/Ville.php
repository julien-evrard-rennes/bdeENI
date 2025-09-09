<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[UniqueEntity(fields: ['nom'], message: 'Ce lieu existe déjà.')]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank(message: 'Le nom est obligatoire.')]
    #[Length (max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 5)]
    #[NotBlank(message: 'Le code postal est obligatoire.')]
    #[Length (max: 5, maxMessage: 'Le code postal ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $codePostal = null;

    #[ORM\OneToMany(mappedBy: "ville", targetEntity: Lieu::class)]
    private ?Collection $lieu;

    public function __construct()
    {
        $this->lieu = new ArrayCollection();
    }
    public function getLieux(): Collection
    {
        return $this->lieu;
    }
    public function addLieu(Lieu $lieu): self
    {
        if (!$this->lieu->contains($lieu)) {
            $this->lieu[] = $lieu;
            $lieu->setVille($this);
        }
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }
}
