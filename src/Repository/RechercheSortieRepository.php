<?php


namespace App\Repository;

use App\Entity\Sortie;
use App\Form\Models\RechercheSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Form\Models\RechercheSortie>
 */

class RechercheSortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects based on the search criteria
     */
    public function rechercheAvancee(RechercheSortie $sortie, $utilisateur): array
    {
        $qb = $this->createQueryBuilder('s');
       $dateDebut = $sortie->getDateHeureDebut();
       $dateFin = $sortie->getDateHeureFin();
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

           $qb ->join('s.organisateur', 'o')
               ->andWhere('s.organisateur = :utilisateur')
               ->setParameter('utilisateur', $utilisateur);
       }

       return $qb->orderBy('s.dateHeureDebut', 'ASC')
           ->getQuery()
           ->getResult();
    }
}