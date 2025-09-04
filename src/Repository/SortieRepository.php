<?php

namespace App\Repository;

use App\Entity\Sortie;
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



   public function rechercheAvancee($campus,
                                    $motCle,
                                    $dateDebut,
                                    $dateFin)
   {
       $qb = $this->createQueryBuilder('s');

       if ($campus) {
              $qb->andWhere('s.campus = :campus')
                 ->setParameter('campus', $campus);
       }
       if ($motCle) {
           $qb->andWhere('s.nom LIKE :motCle')
              ->setParameter('motCle', '%' . $motCle . '%');
       }
       if ($dateDebut) {
           $qb->andWhere('s.dateHeureDebut >= :dateDebut')
              ->setParameter('dateDebut', $dateDebut);
       }
       if ($dateFin) {
           $qb->andWhere('s.dateHeureDebut <= :dateFin')
              ->setParameter('dateFin', $dateFin);
       }

       return $qb->orderBy('s.dateHeureDebut', 'DESC')->getQuery()->getResult();
   }

    public function rechercheParNom(?string $query): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nom LIKE :motCherche')
            ->setParameter('motCherche', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

}
