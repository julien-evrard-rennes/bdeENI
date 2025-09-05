<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\Models\RechercheSortie;
use App\Form\RechercheSortieType;
use App\Repository\EtatRepository;
use App\Repository\RechercheSortieRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function accueil(RechercheSortieRepository $sortieRepository,
                        Request $request): Response
    {
        $sortie = new RechercheSortie();
        $sortieForm = $this->createForm(RechercheSortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        dump($request->request->all());

        // src/Controller/SortieController.php
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie = $sortieForm->getData();
            $utilisateur = $this->getUser();

            $sorties = $sortieRepository->rechercheAvancee($sortie,$utilisateur);

        } else {
            $sorties = $sortieRepository->rechercheBasique();
        }
        
        return $this->render('sortie/accueil.html.twig',[
            'sortieForm' => $sortieForm->createView(),
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
        if($sortie->getEtat()->getLibelle() === 'Annulée') {
            return $this->render('sortie/annulation.html.twig', [
                'sortie' => $sortie,
            ]);
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
                            EtatRepository $etatRepository,
                            EntityManagerInterface $entityManager): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));
        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash("success", 'La sortie "'.$sortie->getNom().'" a bien été annulée.');

        $sorties = $sortieRepository ->findBy([],['dateHeureDebut' => 'DESC']);

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
            $sortie->setParticipants($user);
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

        $this->addFlash("success", 'Vous avez été désinscrit de"'.$sortie->getNom().'.');

        return $this->redirectToRoute('accueil', [
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