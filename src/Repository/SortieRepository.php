<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);

    }

   public function rechercheAvancee($sortie, $utilisateur): array
   {
       $qb = $this->createQueryBuilder('s');
       $campus = $sortie->getCampus();
       $dateDebut = $sortie->getDateDebut();
       $dateFin = $sortie->getDateFin();
       $anciennete = $sortie->getAnciennete();
       $organisateurPresent = $sortie->getOrganisateurPresent();


       if ($sortie->getCampus()) {
              $qb->andWhere('s.campus = :campus')
                 ->setParameter('campus', $sortie->getCampus());

       }
       if ($sortie->getnom()) {
           $motCle = $sortie->getNom();
           $qb->andWhere('s.nom LIKE :motCle')
              ->setParameter('motCle', '%' . $motCle . '%');
       }
       if ($dateDebut && $dateFin) {
           $qb->andWhere('s.dateHeureDebut >= :dateDebut AND s.dateHeureDebut <= :dateFin')
              ->setParameter('dateDebut', $dateDebut)
             ->setParameter('dateFin', $dateFin);
       }
       if(!$anciennete) {
           $qb->join('s.etat', 'e')
               ->andWhere("e.libelle != 'Passée'")
               ->andWhere('s.dateHeureDebut <= :archive')
               ->setParameter('archive', new \DateTime('+ 28 days'));
       }

       if ($organisateurPresent) {

              // Supposons que l'utilisateur connecté est passé en paramètre
              $qb ->join('s.organisateur', 'o')
                  ->andWhere('s.organisateur = :utilisateur')
                 ->setParameter('utilisateur', $utilisateur);
         }

       return $qb->orderBy('s.dateHeureDebut', 'ASC')
           ->getQuery()
           ->getResult();
   }

    public function rechercheParNom(?string $query): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nom LIKE :motCherche')
            ->setParameter('motCherche', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function rechercheBasique()
    {
            $qb = $this->createQueryBuilder('s')
                ->join('s.etat', 'e')
                ->andWhere("e.libelle != 'Passée'")
            ->andWhere('s.dateHeureDebut <= :archive')
            ->setParameter('archive', new \DateTime('+ 28 days'));

             return $qb->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
