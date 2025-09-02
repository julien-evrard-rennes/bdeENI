<?php

namespace App\Entity\Sortie;

use App\Repository\Sortie\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'participants')]
    private Collection $Sorties;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'Sorties')]
    private Collection $participants;

    public function __construct()
    {
        $this->Sorties = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSorties(): Collection
    {
        return $this->Sorties;
    }

    public function addSorty(self $sorty): static
    {
        if (!$this->Sorties->contains($sorty)) {
            $this->Sorties->add($sorty);
        }

        return $this;
    }

    public function removeSorty(self $sorty): static
    {
        $this->Sorties->removeElement($sorty);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(self $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addSorty($this);
        }

        return $this;
    }

    public function removeParticipant(self $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeSorty($this);
        }

        return $this;
    }
}
