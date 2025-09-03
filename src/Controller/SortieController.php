<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function accueil(SortieRepository $sortieRepository,
                        Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(RechercheSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        $filtre= [];
        $filtreNom = [];
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($sortie->getCampus()) {
                $filtre['campus'] = $sortie->getCampus();
            }
            if ($sortie->getNom()) {
                $filtreNom->$sortieRepository->findOneByName($sortie->getNom());
            } else {
                $filtreNom->$sortieRepository->findAll();
            }
        }

       // $sorties = $sortieRepository ->findBy($filtre,['dateHeureDebut' => 'DESC'])
            //-> findQuery($filtreNom);

        $sorties = $sortieRepository->findBy($filtre, $filtreNom);

        return $this->render('sortie/accueil.html.twig',[
            'sortieForm' => $sortieForm,
            'sorties'=> $sorties,
        ]);
    }

    #[Route('/creer', name: 'sortie_creer')]
    public function creer(): Response{
        return $this->render('sortie/creer.html.twig',);
    }

    #[Route('/detail/{id}', name: 'sortie_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, SortieRepository $sortieRepository): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        return $this->render('sortie/detail.html.twig',[
            'sortie'=> $sortie,
        ]);
    }

    #[Route('/modifier/{id}', name: 'sortie_modifier', requirements: ['id' => '\d+'])]
    public function modifier(int $id,
                             SortieRepository $sortieRepository,
    ): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        return $this->render('sortie/modifier.html.twig',[
            'sortie'=> $sortie,
        ]);
    }

    #[Route('/annuler/{id}', name: 'sortie_annuler', requirements: ['id' => '\d+'])]
    public function annuler(int $id,
                            SortieRepository $sortieRepository,
                            EntityManagerInterface $entityManager): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $sortie->setEtat('Annulée');
        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash("success", 'La sortie "'.$sortie->getNom().'" a bien été annulée.');

        $sorties = $sortieRepository ->findBy([],['dateHeureDebut' => 'DESC'], 10);

        return $this->redirectToRoute('accueil', [
            'sorties'=> $sorties,
        ]);
    }

    #[Route('/inscription/{id}', name: 'sortie_inscription', requirements: ['id' => '\d+'])]
    public function inscription(int $id,
                                SortieRepository $sortieRepository,
                                EntityManagerInterface $entityManager): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $user = $this->getUser();
        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash("warning", 'Vous êtes déjà inscrit à la sortie "'.$sortie->getNom().'.');
        } else {
            $sortie->addParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash("success", 'Vous êtes inscrit à la sortie "'.$sortie->getNom().'.');
        }

        $sorties = $sortieRepository ->findBy([],['dateHeureDebut' => 'DESC'], 10);

        return $this->redirectToRoute('accueil', [
            'sorties'=> $sorties,
        ]);
    }

    #[Route('/desinscription/{id}', name: 'sortie_desinscription', requirements: ['id' => '\d+'])]
    public function desinscription(int $id,
                                   SortieRepository $sortieRepository,
                                   EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $user = $this->getUser();
        if ($sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        $sorties = $sortieRepository ->findBy([],['dateHeureDebut' => 'DESC'], 10);

        $this->addFlash("success", 'Vous avez été désinscrit de"'.$sortie->getNom().'.');

        return $this->redirectToRoute('accueil', [
            'sorties'=> $sorties,
        ]);

    }

    #[Route('/supprimer/{id}', name: 'sortie_publier', requirements: ['id' => '\d+'])]
    public function supprimer(int $id,
                              SortieRepository $sortieRepository,
                              EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $entityManager->remove($sortie);
        $entityManager->flush();

        $this->addFlash("success", 'La sortie "' . $sortie->getNom() . '" a bien été supprimée.');

        $sorties = $sortieRepository->findBy([], ['dateHeureDebut' => 'DESC'], 10);

        return $this->redirectToRoute('accueil', [
            'sorties' => $sorties,
        ]);

    }



}