<?php

namespace App\Form\Models;

use App\Entity\Campus;

class RechercheSortie
{
    private ?Campus $campus =null;

    private ?string $nom =null;

    private ?\DateTime $dateHeureDebut =null;

    private ?\DateTime $dateHeureFin =null;

    private ?bool $organisateurPresent =null;
    private ?bool $inscrit =null;
    private ?bool $nonInscrit =null;
    private ?bool $anciennete =null;

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTime $dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    public function getDateHeureFin(): ?\DateTime
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(?\DateTime $dateHeureFin): void
    {
        $this->dateHeureFin = $dateHeureFin;
    }

    public function getOrganisateurPresent(): ?bool
    {
        return $this->organisateurPresent;
    }

    public function setOrganisateurPresent(?bool $organisateurPresent): void
    {
        $this->organisateurPresent = $organisateurPresent;
    }

    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    public function getNonInscrit(): ?bool
    {
        return $this->nonInscrit;
    }

    public function setNonInscrit(?bool $nonInscrit): void
    {
        $this->nonInscrit = $nonInscrit;
    }

    public function getAnciennete(): ?bool
    {
        return $this->anciennete;
    }

    public function setAnciennete(?bool $anciennete): void
    {
        $this->anciennete = $anciennete;
    }


}