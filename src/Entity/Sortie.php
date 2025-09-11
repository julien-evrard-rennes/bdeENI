<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 55)]
    #[NotBlank(message: 'Le nom de la sortie est obligatoire.')]
    #[Length(
        max: 55,
        maxMessage: 'Le nom de la sortie ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[Assert\GreaterThan('now', message: 'La date de sortie doit être ultérieure à aujourd\'hui')]
    #[ORM\Column]
    #[NotBlank(message: 'La date et l\'heure de la sortie sont obligatoires.')]
    private ?\DateTime $dateHeureDebut = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;


    #[ORM\Column]
    #[NotBlank(message: 'Limite d\'inscription doit être obligatoires.')]
    private ?\DateTime $dateLimiteInscription = null;

    #[ORM\Column(length: 4)]
    #[NotBlank(message: 'Le nombre de places disponibles est obligatoire')]
    #[Length(
        max: 4,
        maxMessage: 'Vous ne pouvez pas dépasser les {{ limit }} participants.')]
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
    #[ORM\JoinColumn(name : "organisateur_id", referencedColumnName : "id", onDelete : "CASCADE")]
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

    public function getParticipants(): Collection
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
    public function __toString(): string
    {
        echo 'entrée dans toString';
        $nom = $this->nom ?? 'Sortie';
        $date = $this->dateHeureDebut?->format('d/m/Y H:i') ?? 'date inconnue';
        $lieu = $this->lieu?->getNom() ?? 'lieu inconnu';
        $campus = $this->campus?->getNom() ?? 'campus inconnu';
        $participantsLabel = 'Aucun participant';
        if (isset($this->participants) && $this->participants instanceof \Doctrine\Common\Collections\Collection) {
            $count = $this->participants->count();
            if ($count > 0) {
                $names = [];
                foreach ($this->participants as $p) {
                    if (is_object($p)) {
                        if (method_exists($p, '__toString')) {
                            $names[] = (string) $p;
                            continue;
                        }
                        $prenom = method_exists($p, 'getPrenom') ? $p->getPrenom() : null;
                        $nomP = method_exists($p, 'getNom') ? $p->getNom() : null;
                        $display = trim(implode(' ', array_filter([$prenom, $nomP], static fn($v) => !empty($v))));
                        if ($display === '') {
                            $display = sprintf('#%s', method_exists($p, 'getId') ? (string) $p->getId() : '?');
                        }
                        $names[] = $display;
                    }
                }
                $participantsLabel = sprintf('Participants (%d): %s', $count, implode(', ', $names));
            }
        }



        $segments = array_filter([$nom, $date, $lieu, $campus,$participantsLabel]);
        return implode(' | ', $segments);

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;
        return $this;
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

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeSortie($this);
        }
        return $this;
    }

}
