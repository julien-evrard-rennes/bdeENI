<?php


namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\Models\RechercheSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Form\Models\RechercheSortie>
 */

class RechercheSortieRepository
{
    private SortieRepository $sortieRepository;
    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    public function rechercheAvancee(RechercheSortie $critere, Participant $utilisateur): array
    {
        return $this->sortieRepository->rechercheAvancee($critere, $utilisateur);
    }

    public function rechercheBasique(): array
    {
        return $this->sortieRepository->rechercheBasique();
    }
    }