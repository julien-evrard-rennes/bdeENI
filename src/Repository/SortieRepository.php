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
       dump($sortie);
    dump($utilisateur);

       if ($sortie->getCampus()) {
           dump('Campus');
              $qb->andWhere('s.campus = :campus')
                 ->setParameter('campus', $sortie->getCampus());

       }
       if ($sortie->getnom()) {
           dump('Nom');
           $motCle = $sortie->getNom();
           $qb->andWhere('s.nom LIKE :motCle')
              ->setParameter('motCle', '%' . $motCle . '%');
       }
       if ($dateDebut && $dateFin) {
              dump('Date');
           $qb->andWhere('s.dateHeureDebut >= :dateDebut AND s.dateHeureDebut <= :dateFin')
              ->setParameter('dateDebut', $dateDebut)
             ->setParameter('dateFin', $dateFin);
       }
       if ($inscrit) {
                dump('Inscrit');
              $qb->join('s.participants', 'p')
                ->andWhere('p = :utilisateur')
                ->setParameter('utilisateur', $utilisateur->getId());
       }
       if ($nonInscrit) {
                dump('Non Inscrit');
              $qb->leftJoin('s.participants', 'p')
                 ->andWhere('p != :utilisateur')
                 ->setParameter('utilisateur', $utilisateur->getId());
       }
       if(!$anciennete) {
           $qb->join('s.etat', 'e')
               ->andWhere("e.libelle NOT LIKE 'Historisé'");
       }

       if($anciennete) {
           $oneMonthAgo = (new \DateTimeImmutable())->modify('-1 month');

           dump('Anciennete');
           $qb->join('s.etat', 'e')
               ->andWhere('s.dateHeureDebut < :oneMonthAgo')
               ->setParameter('oneMonthAgo', $oneMonthAgo)
               ->andWhere("e.libelle LIKE 'Historisé'");
           $sorties = $qb->getQuery()->getResult();
           dump($sorties);
           foreach ($sorties as $sortie) {
               if ($sortie->getEtat()->getLibelle() === 'Historisé') {
                   dump($sortie);
               }
               else {
               $sortie->setEtat($this->getEntityManager()->getRepository('App\Entity\Etat')->findOneBy(['libelle' => 'Historisé']));
               $this->getEntityManager()->persist($sortie);
               $this->getEntityManager()->flush();
               }
           }
       }

       if ($organisateurPresent) {
                dump('Organisateur');
              $qb ->join('s.organisateur', 'o')
                  ->andWhere('s.organisateur = :utilisateur')
                 ->setParameter('utilisateur', $utilisateur);
         }

       dump($qb->getQuery()->getSQL());
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
                ->andWhere("e.libelle NOT LIKE 'Historisé'");
             return $qb->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
