<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
#[UniqueEntity(fields: ['libelle'], message: 'Cet état existe déjà.')]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[NotBlank(message: 'Le libelle est obligatoire.')]
    #[Length (max: 255, maxMessage: 'Le libelle ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: "Etat")]
    private ?Collection $sorties = null;

    function __construct()
    {
        $this->sorties = new ArrayCollection();
    }
    public function getSorties(): Collection
    {
        return $this->sorties;
    }
    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
            $sortie->setEtat($this);
        }

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

//    public function getSortie(): ?Sortie
//    {
//        return $this->sortie;
//    }


    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSortie(): ?Sortie
    {
        return $this->sortie;
    }

    public function setSortie(?Sortie $sortie): static
    {
        $this->sortie = $sortie;

        return $this;
    }


}
