<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 55)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTime $dateHeureDebut = null;

    #[ORM\Column(nullable: true)]
    private ?int $durée = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateLimiteInscription = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $infosSortie = null;

    #[ORM\ManyToOne(inversedBy:"Sortie")]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy:"Sortie")]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy:"Sortie")]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy:"Sortie")]
    private ?Participant $organisateur = null;

    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: "sorties")]
    #[ORM\JoinTable(name: "sortie_participant")]
    private Collection $participants;

    /**
     * @var Collection<int, Etat>
     */
    #[ORM\OneToMany(targetEntity: Etat::class, mappedBy: 'sortie')]
    private Collection $etats;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->etats = new ArrayCollection();
    }

    public function getParticipants(): string
    {
        return $this->participants;
    }
    public function setParticipants(Participant $participant): self {
            echo 'Entrée dans setParticipants';
        if(!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->addSortie($this);
        }
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }
    public function setEtat(?Etat $etat): void
    {
        $this->etat = $etat;
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

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTime $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDurée(): ?int
    {
        return $this->durée;
    }

    public function setDurée(?int $durée): void
    {
        $this->durée = $durée;

    }


    public function getDateLimiteInscription(): ?\DateTime
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTime $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(?int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }
    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): void
    {
        $this->lieu = $lieu;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return Collection<int, Etat>
     */
    public function getEtats(): Collection
    {
        return $this->etats;
    }

    public function addEtat(Etat $etat): static
    {
        if (!$this->etats->contains($etat)) {
            $this->etats->add($etat);
            $etat->setSortie($this);
        }

        return $this;
    }

    public function removeEtat(Etat $etat): static
    {
        if ($this->etats->removeElement($etat)) {
            // set the owning side to null (unless already changed)
            if ($etat->getSortie() === $this) {
                $etat->setSortie(null);
            }
        }

        return $this;
    }

}
