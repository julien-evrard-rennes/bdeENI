<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;

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
       $dateDebut = $sortie->getDateHeureDebut();
       $dateFin = $sortie->getDateHeureFin();
       $anciennete = $sortie->getAnciennete();
       $organisateurPresent = $sortie->getOrganisateurPresent();
       $inscrit = $sortie->getInscrit();
       $nonInscrit = $sortie->getNonInscrit();

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
       if($inscrit && $nonInscrit) {
              $qb->join('s.participants', 'p')
                ->andWhere(':utilisateur MEMBER OF s.participants OR :utilisateur NOT MEMBER OF s.participants')
                ->setParameter('utilisateur', $utilisateur->getId());
       }
       elseif ($inscrit) {
              $qb->join('s.participants', 'p')
                ->andWhere(':utilisateur MEMBER OF s.participants')
                ->setParameter('utilisateur', $utilisateur->getId());
       }
       elseif ($nonInscrit) {
              $qb->join('s.participants', 'm')
                 ->andWhere(':utilisateur NOT MEMBER OF s.participants')
                 ->setParameter('utilisateur', $utilisateur->getId());
       }
       if(!$anciennete) {
           $qb->join('s.etat', 'e')
               ->andWhere("e.libelle NOT LIKE 'Historisée'");
       }

       if($anciennete) {
           $oneMonthAgo = (new \DateTimeImmutable())->modify('-1 month');

           $qb->join('s.etat', 'e')
               ->andWhere("e.libelle LIKE 'Historisée'");
           $sorties = $qb->getQuery()->getResult();
           foreach ($sorties as $sortie) {
               if ($sortie->getEtat()->getLibelle() != 'Historisée') {
               $sortie->setEtat($this->getEntityManager()->getRepository('App\Entity\Etat')->findOneBy(['libelle' => 'Historisée']));
               $this->getEntityManager()->persist($sortie);
               $this->getEntityManager()->flush();
               }
           }
       }

       if ($organisateurPresent) {
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
                ->andWhere("e.libelle NOT LIKE 'Historisée'");
             return $qb->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
